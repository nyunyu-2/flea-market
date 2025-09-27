<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Trade;

use App\Http\Requests\ProfileRequest;


class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ユーザーが見つかりません');
        }

        $page = $request->query('page', 'sell');

        $soldItems = collect();
        $boughtItems = collect();
        $tradingItems = collect();

        $purchasesTrading = $user->purchases()
            ->where('status', ['trading', 'buyer_completed'])
            ->with('item', 'trade.messages')
            ->get();

        $sellingTrading = Trade::with('purchase.item', 'messages')
            ->whereHas('purchase', function($q) use ($user) {
                $q->whereHas('item', fn($iq) => $iq->where('user_id', $user->id))
                ->where(function($q2) {
                    $q2->where('status', 'trading')
                        ->orWhere(function($q3) {
                            $q3->where('status', 'buyer_completed')
                                ->whereNull('seller_rating');
                        });
                });
            })
            ->get();

        foreach ($purchasesTrading as $purchase) {
            if (!$purchase->trade) {
                $trade = Trade::create(['purchase_id' => $purchase->id]);
                $purchase->trade()->associate($trade);
                $purchase->save();
            }
        }

        $tradingItems = $purchasesTrading->merge($sellingTrading)
            ->keyBy(function($item) {
                return $item instanceof \App\Models\Trade
                    ? 'trade_'.$item->id
                    : 'purchase_'.$item->id;
            });

        foreach ($tradingItems as $tradeOrPurchase) {
            $trade = $tradeOrPurchase instanceof \App\Models\Trade
                ? $tradeOrPurchase
                : $tradeOrPurchase->trade ?? null;

            $tradeOrPurchase->unread_count = $trade
                ? $trade->messages()->where('user_id', '<>', $user->id)->whereNull('read_at')->count()
                : 0;
        }

        // --- 未読件数合計 ---
        $totalUnreadCount = $tradingItems->sum(fn($tradeOrPurchase) => $tradeOrPurchase->unread_count ?? 0);

        // --- ページごとのアイテム取得 ---
        if ($page === 'sell') {
            $soldItems = $user->products()->latest()->get();
        } elseif ($page === 'buy') {
            $boughtItems = $user->purchases()->latest()->get();
        }

        $averageRating = $user->averageRating();

        return view('mypage.mypage', [
            'soldProducts' => $soldItems,
            'boughtProducts' => $boughtItems,
            'tradingProducts'=> $tradingItems,
            'totalUnreadCount' => $totalUnreadCount,
            'page' => $page,
            'averageRating' => $averageRating,
            'user' => $user,
        ]);
    }

    public function editProfile()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ログインしてください');
        }

        return view('mypage.profile_edit', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ログインしてください');
        }

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->fill($validated);
        $user->save();

        return redirect($request->input('redirect_to', '/mypage'))->with('success', 'プロフィールを更新しました');
    }
}
