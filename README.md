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

## ダミーデータ作成方法

開発環境で以下コマンドを実行してダミーデータを作成してください。

- php artisan db:seed
- php artisan db:seed --class=ItemSeeder

## テスト方法

- php artisan test

## 🔐 ログイン情報（開発用）

### 管理者ユーザー

- メールアドレス：`admin@example.com`
- パスワード：`password`

### 一般ユーザー

- メールアドレス：`user@example.com`
- パスワード：`password`
