@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css')}}">
@endsection

@section('content')
<div class="sell-form__content">
    <div class="sell-form__title">
        <h2>商品の出品</h2>
    </div>
    <form class="sell-form__form" method="POST"  action="{{ route('items.store') }}"  enctype="multipart/form-data">
        @csrf
        <div class="sell-form__section">
            <label>商品画像</label>
            <div class="image-upload">
                <img id="previewImage"
                    src="{{ !empty($item) && $item->image_path ? asset('storage/' . $item->image_path) : '' }}"
                    alt="商品画像プレビュー"
                    style="max-width:300px; @if(empty($item) || !$item->image_path) display:none; @endif">
                <label for="image">画像を選択する</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
        </div>
        <div class="sell-form__section">
            <h2>商品の詳細</h2>
            <label>カテゴリー</label>
            <div class="category">
                @foreach ($categories as $category)
                    <div class="category__item">
                        <input
                            type="checkbox"
                            id="category{{ $category->id }}"
                            name="categories[]"
                            value="{{ $category->id }}"
                            {{ !empty($item) && $item->categories->contains($category->id) ? 'checked' : '' }}
                        >
                        <label for="category{{ $category->id }}">{{ $category->name }}</label>
                    </div>
                @endforeach
            </div>

            <label for="status">商品の状態</label>
            <div class="select">
                <select id="status" name="status" required>
                    <option value=""disabled {{ old('status', $item->status ?? '') === '' ? 'selected' : '' }} hidden>選択してください</option>
                    <option value="良好" {{ old('status', $item->status ?? '') === '良好' ? 'selected' : '' }}>良好</option>
                    <option value="目立った傷や汚れなし" {{ old('status', $item->status ?? '') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり" {{ old('status', $item->status ?? '') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                    <option value="状態が悪い" {{ old('status', $item->status ?? '') === '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
                </select>
            </div>
        </div>

        <div class="sell-form__section">
            <h2>商品名と説明</h2>
            <div class="sell-form__section-explanation">
                <label for="name">商品名</label>
                <input type="text" id="name"  name="name"/>

                <label for="brand">ブランド名</label>
                <input type="text" id="brand" name="brand"/>

                <label for="description">商品の説明</label>
                <textarea id="description" name="description"></textarea>

                <label for="price">販売価格</label>
                <div class="sell-form__section--input-wrap">
                    <span class="yen">¥</span>
                    <input type="number" id="price" name="price" min="0" step="1">
                </div>
            </div>
        </div>

        <button class="sell-form__button-primary">出品する</button>
    </form>
</div>
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('previewImage');

    if (!file) {
        preview.style.display = 'none';
        preview.src = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>

@endsection