@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')
<div class="form__content">
    <div class="form__heading">
        <input type="radio" name="page" id="recommendation"
        {{ $page === 'recommendation' ? 'checked' : '' }}
        onclick="location.href='{{ route('items.index') }}'">
        <label for="recommendation" class="form__recommendation">おすすめ</label>

        <input type="radio" name="page" id="mylist"
        {{ $page === 'mylist' ? 'checked' : '' }}
        onclick="location.href='{{ route('items.index', ['page' => 'mylist']) }}'">
        <label for="mylist" class="form__my-list">マイリスト</label>
    </div>
    <div class="items-grid">
        @forelse ($items as $item)
            <div class="item-card">
                <a href="{{ route('items.show', ['item' => $item->id]) }}">
                    <div class="item-card__image-wrapper">
                        @if ($item->image_path)
                            <img class="item-card__image" src="{{ asset('storage/' . $item->image_path) }}" alt="商品画像">
                        @else
                            <div class="item-card__image no-image">商品画像</div>
                        @endif

                        @if ($item->is_sold)
                            <div class="item-card__label--sold">SOLD</div>
                        @endif
                    </div>
                </a>
                <div class="item-card__name">{{ $item->name }}</div>
            </div>
        @empty
            <p>商品が見つかりません</p>
        @endforelse
    </div>
</div>
@endsection
