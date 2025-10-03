<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Trade;
use App\Http\Requests\TradeMessageRequest;

use App\Models\TradeMessage;
use Illuminate\Support\Facades\Auth;

class TradeController extends Controller
{
    public function show(Purchase $purchase)
    {
        $purchase->load('user', 'item.user', 'trade.messages.user');

        $trade = $purchase->trade;

        if (!$trade) {
            $trade = Trade::create([
                'purchase_id' => $purchase->id,
            ]);
            $purchase->trade()->associate($trade);
            $purchase->save();
        }

        $trade->messages()
            ->where('user_id', '<>', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $trade->messages()->with('user')->get();

        $loginUserId = auth()->id();

        $partner = $purchase->user_id === $loginUserId
            ? $purchase->item->user
            : $purchase->user;

        $seller = $purchase->item->user;

        $otherPurchases = Purchase::whereHas('item', function ($q) use ($seller) {
                $q->where('user_id', $seller->id);
            })
            ->where('id', '!=', $purchase->id)
            ->with(['item', 'trade'])
            ->get();

        return view('mypage.trades.show', compact('purchase', 'trade', 'messages', 'partner', 'otherPurchases'));
    }

    public function store(TradeMessageRequest $request, Purchase $purchase)
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('trade_images', 'public');
        }

        $trade = $purchase->trade;

        $message = $trade->messages()->create([
            'trade_id' => $trade->id,
            'user_id'  => Auth::id(),
            'message'  => $request->message ?? '',
            'image_path' => $imagePath,
        ]);

        $message->load('user');

        return redirect()
            ->back()
            ->with('success', 'メッセージを送信しました');
    }

    public function update(Request $request, $id)
    {
        $message = TradeMessage::findOrFail($id);

        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message->message = $request->message;
        $message->save();

        return response()->json(['message' => $message->message]);
    }

    public function destroy($id)
    {
        $message = TradeMessage::findOrFail($id);

        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }
}

