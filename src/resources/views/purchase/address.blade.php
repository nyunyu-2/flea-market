@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
@endsection

@section('content')
<div class="address-form">
    <div class="address-form__heading">
        <h2>住所の変更</h2>
    </div>
    <form class="address-form__form" method="POST" action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}">
        @csrf
        <div class="address-form__form-group">
            <input type="hidden" name="redirect_to" value="{{ route('purchase.show', ['item' => $item->id]) }}">

            <label for="zipcode">郵便番号</label>
            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', Auth::user()->zipcode) }}">

            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', Auth::user()->address) }}">

            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', Auth::user()->building) }}">
        </div>

        <button class="address-form__submit-button" type="submit">更新する</button>
    </form>
</div>
@endsection