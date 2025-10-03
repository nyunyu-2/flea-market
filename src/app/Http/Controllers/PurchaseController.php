<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Models\Trade;
use App\Mail\TradeCompletedMail;
use Illuminate\Support\Facades\Mail;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show(Item $item)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return view('purchase.purchase', compact('item'));
    }

    public function store(PurchaseRequest $request, $item_id)
    {
        try {
            $user = Auth::user();
            $paymentMethod = $request->input('payment_method');
            $item = Item::findOrFail($item_id);

            if ($item->is_sold) {
                return redirect()->back()->withErrors(['item' => 'この商品はすでに購入されています。']);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => [$paymentMethod],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item_id' => $item->id]),
                'cancel_url' => route('purchase.cancel', ['item_id' => $item->id]),
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            \Log::error('Stripeエラー: ' . $e->getMessage());
            return redirect()->back()->withErrors(['stripe' => '決済処理に失敗しました: ' . $e->getMessage()]);
        }
    }

    public function success($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        $zipcode = $user->zipcode;
        $address = $user->address;
        $building = $user->building;

        if (empty($zipcode) || empty($address)) {
            return redirect()->back()->withErrors(['address' => '住所情報は必須です']);
        }

        $item->is_sold = true;
        $item->save();

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'zipcode' => $user->zipcode,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        return redirect()->route('items.index');
    }

    public function handleSuccess($item_id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // --- ここで Purchase 作成 ---
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'zipcode' => $user->zipcode,
            'address' => $user->address,
            'building' => $user->building,
            'status'  => 'trading',
        ]);

        // --- ここで Trade 作成 ---
        $trade = Trade::create([
            'purchase_id' => $purchase->id,
        ]);

        $purchase->trade_id = $trade->id;
        $purchase->save();

        $item->is_sold = true;
        $item->save();

        // チャット画面へリダイレクト
        return redirect()->route('trades.show',  ['purchase' => $purchase]);
    }


    public function editAddress($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);

        return view('purchase.address', compact('user', 'item'));
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        $validated = $request->validated();

        $user = auth()->user();
        $user->fill($validated);
        $user->save();

        return redirect()->route('purchase.show', ['item' => $item_id]);
    }

    public function rate(Request $request, Purchase $purchase)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = auth()->id();

        // 購入者が評価 → 出品者への評価
        if ($userId === $purchase->user_id) {
            if (is_null($purchase->seller_rating)) {
                $purchase->seller_rating = $request->rating;
                $purchase->seller_comment = $request->comment;
                $purchase->status = 'buyer_completed';

                $purchase->save();

                // 出品者に通知メール送信
                Mail::to($purchase->item->user->email)
                    ->send(new TradeCompletedMail($purchase));
            } else {
                abort(403, '既に評価済みです。');
            }
        }
        // 出品者が評価 → 購入者への評価
        elseif ($userId === $purchase->item->user_id) {
            if ($purchase->status === 'buyer_completed' && is_null($purchase->buyer_rating)) {
                $purchase->buyer_rating = $request->rating;
                $purchase->buyer_comment = $request->comment;
                $purchase->status = 'completed'; // 両者評価済み

                $purchase->save();
            } else {
                abort(403, '評価できません。');
            }
        } else {
            abort(403, 'この取引の関係者ではありません。');
        }

        return redirect()->route('items.index')
                        ->with('success', '評価を送信しました。');
    }

}
