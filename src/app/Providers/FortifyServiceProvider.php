<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;

use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect('/mypage/profile?from=register');
                }
            };
        });

        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect('/');
                }
            };
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::authenticateUsing(function ($request) {
            $rules = [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ];

            $messages = [
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => '有効なメールアドレスを入力してください',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
            ];

            Validator::make($request->all(), $rules, $messages)->validate();

            if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }

            return Auth::user();
        });


        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(1000); // 制限緩和（本番ではやらない）
        });
    }
}
