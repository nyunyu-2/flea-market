<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
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
        <div class="verify-form">
            <div class="verify-form__message">
                <h2>登録していただいたメールアドレスに認証メールを送付しました。</h2>
                <h2>メール認証を完了してください</h2>
            </div>
            <div class="verify-form__send-button">
                <a href="{{ url('/email/verify') }}">
                    認証はこちらから
                </a>
            </div>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="verify-form__resend-button">
                    <button>
                        認証メールを再送する
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>