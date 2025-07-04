<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" style="height: 36px;">
            </a>
            <div class="header__search">
                <form action="{{ route('items.index') }}" method="GET">
                    <input
                        class="header__search-text"
                        type="text"
                        name="keyword"
                        placeholder="何をお探しですか？"
                        value="{{ request('keyword') }}"
                    >
                </form>
            </div>
            <div class="header__nav">
                @guest
                    {{-- ログインしていない人 --}}
                    <a href="{{ route('login') }}" class="header__login-button">ログイン</a>
                    <a href="{{ route('login') }}" class="header__mypage-button">マイページ</a>
                    <a href="{{ route('login') }}" class="header__sell-button">出品</a>
                @endguest

                @auth
                    {{-- ログインしている人 --}}
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="header__logout-button">ログアウト</button>
                    </form>
                    <a href="{{ url('/mypage') }}" class="header__mypage-button">マイページ</a>
                    <a href="{{ url('/sell') }}" class="header__sell-button">出品</a>
                @endauth
            </div>
        </div>
    </header>
    <div class="content">
        @yield('content')
    </div>
</body>