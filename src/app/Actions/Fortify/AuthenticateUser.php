<?php

namespace App\Actions\Fortify;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

class AuthenticateUser implements AuthenticatesUsers
{
    public function authenticate(array $input)
    {
        $request = new LoginRequest();
        $request->merge($input);
        $request->validateResolved();

        if (! Auth::attempt([
            'email' => $input['email'],
            'password' => $input['password'],
        ], $input['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません。'],
            ]);
        }
    }
}
