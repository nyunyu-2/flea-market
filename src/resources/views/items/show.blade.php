@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css')}}">

@endsection

@section('content')
    <div class="item-detail">
        <div class="item-detail__content">
            <div class="item-detail__img">
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" >
            </div>
            <div class="item-detail__section">
                <div class="item-detail__title">
                    <h1>{{ $item->name }}</h1>
                    <p>{{ $item->brand }}</p>
                </div>
                <div class="item-detail__price">
                    <p>¥<span>{{ number_format($item->price) }}</span>(税込)</p>
                </div>
                <div class="item-detail__icon">
                    {{-- いいねアイコン＋数 --}}
                    <div class="item-detail__icon-block">
                        @if(auth()->check())
                            @php $liked = $item->isLikedBy(Auth::user()); @endphp

                            <form action="{{ $liked ? route('items.unlike', $item->id) : route('items.like', $item->id) }}" method="POST">
                                @csrf
                                @if($liked)
                                    @method('DELETE')
                                    <button type="submit" class="item-detail__star-btn liked"></button>
                                @else
                                    <button type="submit" class="item-detail__star-btn"></button>
                                @endif
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="item-detail__star-btn"></a>
                        @endif

                        <div class="item-detail__icon-count">{{ $item->likes->count() }}</div>
                    </div>

                    {{-- コメントアイコン --}}
                    <div class="item-detail__icon-block">
                        <span class="item-detail__comment-icon">
                            <img src="{{ asset('images/fukidashi.png') }}" alt="comment" />
                        </span>
                        <div class="item-detail__icon-count">{{ $item->comments->count() }}</div>
                    </div>
                </div>
                <a href="{{ route('purchase.show', ['item' => $item->id]) }}" class="item-detail__buy-button">購入手続きへ</a>
                <div class="item-detail__description">
                    <h2>商品の説明</h2>
                    <p>{!! nl2br(e($item->description)) !!}</p>
                </div>
                <div class="item-detail__information">
                    <h2>商品の状態</h2>
                    <div class="item-detail__information-category">
                        <h3>カテゴリー</h3>
                        <div class="item-detail__category-list">
                            @foreach ($item->categories as $category)
                                <span class="item-detail__category-tag">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="item-detail__information-status">
                        <h3>商品の状態</h3>
                        <p>{{ $item->status }}</p>
                    </div>
                </div>
                <div class="item-detail__comment">
                    <h2>コメント({{ $comments->count() }})</h2>

                    @foreach ($comments as $comment)
                        <div class="item-detail__comment-container">
                            <div class="item-detail__comment-container-avatar">
                                <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="ユーザーアイコン" class="item-detail__comment-avatar">
                                <div class="item-detail__comment-author">{{ $comment->user->username }}</div>
                            </div>
                            <div class="item-detail__comment-container-comment">
                                <div class="item-detail__comment-content">
                                    {{ $comment->body }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @auth
                        <div class="item-detail__comment-auth">
                            <h3>商品へのコメント</h3>
                            <form action="{{ route('comments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <textarea name="body" rows="4">{{ old('body') }}</textarea>
                                @if ($errors->has('body'))
                                    <div class="item-detail__error-message">{{ $errors->first('body') }}</div>
                                @endif
                                <button class="item-detail__comment-submit" type="submit">
                                    コメントを送信する
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection