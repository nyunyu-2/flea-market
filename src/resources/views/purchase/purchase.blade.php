@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css')}}">
@endsection

@section('content')
<form action="{{ route('purchase.store', ['item_id' => $item->id]) }}" method="POST">
    @csrf
    <div class="purchase__container">
        <div class="purchase__product-section">
            <div class="purchase__content">
                <div class="purchase__content-image">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" >
                </div>
                <div class="purchase__content-details">
                    <div class="purchase__content-details-title">{{ $item->name }}</div>
                    <p>¥ {{ number_format($item->price) }}</p>
                </div>
            </div>
            <div class="purchase__content-payment">
                <div class="purchase__content-payment-title">支払い方法</div>
                <select id="payment_method" name="payment_method" required>
                    <option value="" disabled hidden {{ old('payment_method') ? '' : 'selected' }}>選択してください</option>
                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>カード支払い</option>
                    <option value="konbini" {{ old('payment_method') == 'konbini' ? 'selected' : '' }}>コンビニ支払い</option>
                </select>

                @error('payment_method')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="purchase__content-address">
                <div class="purchase__address-block">
                    <div class="purchase__address-block-title">配送先 </div>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}">
                        変更する
                    </a>
                </div>
                <div class="purchase__address">
                    <p>〒 {{ Auth::user()->zipcode }}<br>{{ Auth::user()->address }}{{ Auth::user()->building }}</p>
                </div>
            </div>
        </div>
        <div class="purchase__payment-section">
            <table class="purchase__payment-table">
                <tr>
                    <td>商品代金</td>
                    <td>¥ {{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <td>支払い方法</td>
                    <td id="selected_payment_method_label">
                        {{ $paymentLabels[old('payment_method')] ?? '' }}
                    </td>
                </tr>
            </table>
            <button type="submit" class="purchase__btn">購入する</button>
        </div>
    </div>
</form>

<script>
    const paymentLabels = {
        card: 'カード支払い',
        konbini: 'コンビニ支払い'
    };

    const selectElement = document.getElementById('payment_method');
    const labelElement = document.getElementById('selected_payment_method_label');

    selectElement.addEventListener('change', function () {
        const selectedValue = this.value;
        labelElement.textContent = paymentLabels[selectedValue] ?? '';
    });
</script>
@endsection