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
            <div class="mypage__profile-username">{{ Auth::user()->username }}</div>
        </div>
        <button class="mypage__profile-edit-button" onclick="location.href='{{ route('mypage.profile') }}'">プロフィールを編集</button>
    </div>

    <div class="mypage__tabs">
        <div class="mypage__tabs-title">
            <a href="{{ route('mypage') }}?page=sell" class="{{ $page === 'sell' ? 'active' : '' }}">出品した商品</a>
            <a href="{{ route('mypage') }}?page=buy" class="{{ $page === 'buy' ? 'active' : '' }}">購入した商品</a>
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
        @endif
    </div>
</div>

@endsection