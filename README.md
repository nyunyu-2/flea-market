# フリマアプリ

## 使用技術(実行環境)

- PHP 7.4.9
- Laravel 8.83.8
- MySQL 15.1
- Docker 27.5.1
- Composer 2.8.8
- Visual Studio Code (任意)

## 環境構築

1. git clone でリポジトリをクローン
2. docker-compose up -d --build
3. docker-compose exec php bash
4. composer install
5. .env.example をコピーして .env を作成
6. .env ファイルを必要に応じて修正
7. php artisan migrate
8. php artisan key:generate
9. php artisan db:seed

## 認証について

このアプリでは [Laravel Fortify] を使用して、
ユーザー認証機能（会員登録・ログイン・ログアウト）を実装しています。

- composer require laravel/fortify
- php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
- php artisan migrate

- 認証機能のバックエンドはFortifyが提供し、フロントはBladeテンプレートで実装しています。
- バリデーションはフォームリクエストを使用しています。

## ダミーデータユーザー

- ユーザー名 メールアドレス	パスワード

#### 一般ユーザー1	yamada
	yamada@example.com
	password123
#### 一般ユーザー2	suzuki
	suzuki@example.com
	password123
#### 一般ユーザー3	tanaka
    tanaka@example.com
	password123

## ダミー商品データ

#### 一般ユーザー1 yamada
	| 商品名       | ブランド | 価格   | 状態               |
    | ------------ | -------- | ------ | ---------------- |
    | 腕時計       | TOKEI    | 15,000 | 良好             |
    | HDD          | ハードディスク | 5,000  | 目立った傷や汚れなし |
    | 玉ねぎ3束    | 野菜     | 300    | やや傷や汚れあり  |
    | 革靴         | くつ     | 4,000  | 状態が悪い       |
    | ノートPC     | DELL     | 45,000 | 良好             |
#### 一般ユーザー2 suzuki
	| 商品名          | ブランド | 価格   | 状態               |
    | --------------- | -------- | ------ | ---------------- |
    | マイク          | YAMAHA   | 8,000  | 目立った傷や汚れなし |
    | ショルダーバッグ | UNIQLO   | 3,500  | やや傷や汚れあり  |
    | タンブラー      | ニトリ   | 2,000  | 状態が悪い       |
    | コーヒーミル    | ニトリ   | 3,000  | 良好             |
    | メイクセット    | DIOR     | 2,500  | 目立った傷や汚れなし |
#### 一般ユーザー3 tanaka
    なし

## ダミーデータ作成方法

開発環境で以下コマンドを実行してダミーデータを作成してください。

- php artisan migrate:fresh --seed

## テスト方法

- php artisan test

## 🔐 ログイン情報（開発用）

### 管理者ユーザー

- メールアドレス：`admin@example.com`
- パスワード：`password`

### 一般ユーザー

- メールアドレス：`user@example.com`
- パスワード：`password`

## 環境変数の設定

本アプリでは **Stripe** と **Pusher** を利用しています。
支払い機能やリアルタイム通信を利用するために、以下の環境変数を設定してください。

1. `.env.example` をコピーして `.env` を作成します。

```bash
cp .env.example .env
```

2. .env に以下を追記してください（実際の値は Stripe / Pusher の管理画面から取得してください）。
MIX_PUSHER_APP_KEY=your_pusher_app_key
MIX_PUSHER_APP_CLUSTER=your_pusher_app_cluster

STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

## メール送信（開発環境）

- SMTP ホスト: `flea-market-mailhog-1`
- ポート: `1025`
- Web UI: `http://localhost:8025`

### .env 例:MAIL_MAILER=smtp
    MAIL_HOST=flea-market-mailhog-1
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS="test@example.com"
    MAIL_FROM_NAME="Test"