<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Trade;
use App\Models\Purchase;


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
            ->whereIn('status', ['trading', 'buyer_completed'])
            ->get();

        $sellingTrading = Purchase::whereHas('item', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where(function($q) {
                $q->where('status', 'trading')
                ->orWhere(function($q2) {
                    $q2->where('status', 'buyer_completed')
                        ->whereNull('buyer_rating'); // ここポイント！
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

            $tradeOrPurchase->latest_message_at = $trade
                ? $trade->messages()->latest('created_at')->value('created_at')
                : null;
        }

        $totalUnreadCount = $tradingItems->sum(fn($tradeOrPurchase) => $tradeOrPurchase->unread_count ?? 0);
        $tradingItems = $tradingItems->sortByDesc('latest_message_at');

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
