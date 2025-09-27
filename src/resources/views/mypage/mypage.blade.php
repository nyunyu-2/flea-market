@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css')}}">
@endsection

@section('content')
<div class="mypage">
    <div class="mypage__profile-section">
        <div class="mypage__profile">
            <div class="mypage__profile-avatar"
                @if (Auth::user()->profile_image)
                    style="background-image: url('{{ asset('storage/' . Auth::user()->profile_image) }}'); background-size: cover; background-position: center;"
                @else
                    style="background-color: #D9D9D9;"
                @endif
            ></div>
            <div class="mypage__profile-info">
                <div class="mypage__profile-username">{{ Auth::user()->username }}</div>
                @php
                    $average = $user->averageRating();
                    $roundedAverage = round($average);
                @endphp
                @if (!is_null($average))
                    <div class="mypage__profile-rating" style="margin-top: 10px;">
                        {{-- 星を表示 --}}
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $average)
                                <span style="color: gold;">★</span>
                            @else
                                <span style="color: #ccc;">★</span>
                            @endif
                        @endfor
                    </div>
                @endif
            </div>
        </div>
        <button class="mypage__profile-edit-button" onclick="location.href='{{ route('mypage.profile') }}'">プロフィールを編集</button>
    </div>

    <div class="mypage__tabs">
        <div class="mypage__tabs-title">
            <a href="{{ route('mypage') }}?page=sell" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
            <a href="{{ route('mypage') }}?page=buy" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
            <a href="{{ route('mypage') }}?page=trading" class="{{ $page === 'trading' ? 'active' : '' }}">
                取引中の商品
                @if(!empty($totalUnreadCount) && $totalUnreadCount > 0)
                    <span class="tab-badge">{{ $totalUnreadCount }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="mypage__items">
        @if ($page === 'sell')
            @forelse ($soldProducts as $item)
                <div class="mypage__item-card">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                    <p>{{ $item->name }}</p>
                </div>
            @empty
                <p>出品した商品はありません。</p>
            @endforelse
        @elseif ($page === 'buy')
            @forelse ($boughtProducts as $purchase)
                <div class="mypage__item-card">
                    <img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->name }}">
                    <p>{{ $purchase->item->name }}</p>
                </div>
            @empty
                <p>購入した商品はありません。</p>
            @endforelse
        @elseif ($page === 'trading')
            @forelse ($tradingProducts as $tradeOrPurchase)
                @php
                    if ($tradeOrPurchase instanceof \App\Models\Trade) {
                        $purchase = $tradeOrPurchase->purchase ?? null;
                        $item = $purchase->item ?? null;
                        $purchaseId = $purchase->id ?? null;
                        $status = $purchase->status ?? null;
                    } else {
                        $item = $tradeOrPurchase->item ?? null;
                        $purchaseId = $tradeOrPurchase->id;
                        $status = $tradeOrPurchase->status ?? null;
                    }
                    $unreadCount = $tradeOrPurchase->unread_count ?? 0;
                @endphp

                @if($item && in_array($status, ['trading', 'buyer_completed']))
                    <a href="{{ route('trades.show', $purchaseId) }}" class="mypage__item-card">
                        @if($unreadCount > 0)
                            <div class="mypage__item-card-badge">
                                <span class="badge">{{ $unreadCount }}</span>
                            </div>
                        @endif
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}">
                        <p>{{ $item->name }}</p>
                    </a>
                @endif
            @empty
                <p>取引中の商品はありません。</p>
            @endforelse
        @endif
    </div>
</div>

@endsection