<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前が入力されていないときはバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'username' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function メールアドレスが入力されていないときはバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function パスワードが入力されていないときはバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードが7文字以下のときはバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function パスワードと確認用パスワードが一致しないときはバリデーションエラーになる()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function 正しい入力で会員登録できてプロフィール設定画面にリダイレクトされる()
    {
        $response = $this->post('/register', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('mypage.profile', ['from' => 'register']));

        $this->assertDatabaseHas('users', [
            'username' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
    }
}
