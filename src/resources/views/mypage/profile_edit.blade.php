@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css')}}">
@endsection

@section('content')
<div class="profile-form__content">
    <div class="profile-form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="profile-form__form" method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="redirect_to" value="{{ request('from') === 'register' ? '/' : url('/mypage') }}">
        <div class="profile-form__form-group--avatar">
            <div class="profile-form__avatar">
                <img id="avatarPreview" src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/default_icon.png') }}" alt="" class="profile-form__avatar-img">
            </div>
            <div class="profile-form__avatar-button">
                <label for="image">画像を選択する</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>
        </div>
        <div class="profile-form__form-group">
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="{{ old('username',$user->username) }}">

            <label for="zipcode">郵便番号</label>
            <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode', $user->zipcode) }}">

            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}">

            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $user->building) }}">
        </div>
        <button type="submit" class="profile-form__submit-button">更新する</button>
    </form>
</div>
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = function(e) {
        document.getElementById('avatarPreview').src = e.target.result;
    };

    reader.readAsDataURL(file);
});
</script>

@endsection