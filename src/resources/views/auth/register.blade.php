<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" style="height: 36px;">
            </a>
        </div>
    </header>
    <main>
        <div class="register-form__content">
            <div class="register-form__heading">
                <h2 class="register-form__title">会員登録</h2>
            </div>
            <form class="register-form__form" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="register-form__group">
                    <div class="register-form__label-wrapper">
                        <span class="register-form__label">ユーザー名</span>
                    </div>
                    <div class="register-form__field">
                        <div class="register-form__field--text">
                            <input type="text" name="username" value="{{ old('username') }}" />
                        </div>
                        <div class="register-form__error">
                            @error('username')
                                <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__label-wrapper">
                        <span class="register-form__label">メールアドレス</span>
                    </div>
                    <div class="register-form__field">
                        <div class="register-form__field--text">
                            <input type="text" name="email" value="{{ old('email') }}" />
                        </div>
                        <div class="register-form__error">
                            @error('email')
                                <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__label-wrapper">
                        <span class="register-form__label">パスワード</span>
                    </div>
                    <div class="register-form__field">
                        <div class="register-form__field--text">
                            <input type="password" name="password" />
                        </div>
                        <div class="register-form__error">
                            @error('password')
                                <span>{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="register-form__group">
                    <div class="register-form__label-wrapper">
                        <span class="register-form__label">確認用パスワード</span>
                    </div>
                    <div class="register-form__field">
                        <div class="register-form__field--text">
                            <input type="password" name="password_confirmation" />
                        </div>
                    </div>
                </div>
                <div class="register-form__button-wrapper">
                    <button class="register-form__button-register" type="submit">登録する</button>
                    <a href="/login" class="register-form__button-login" type="submit">ログインはこちらから</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>