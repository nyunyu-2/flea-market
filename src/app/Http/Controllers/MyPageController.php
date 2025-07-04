<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;


class MyPageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ユーザーが見つかりません');
        }

        $page = $request->query('page', 'sell');

        $soldItems = collect();
        $boughtItems = collect();

        if ($page === 'sell') {
            $soldItems = $user->products()->latest()->get();
        } elseif ($page === 'buy') {
            $boughtItems = $user->purchases()->latest()->get();
        }

        return view('mypage.mypage', [
            'soldProducts' => $soldItems,
            'boughtProducts' => $boughtItems,
            'page' => $page,
        ]);
    }

    public function editProfile()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ログインしてください');
        }

        return view('mypage.profile_edit', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'ログインしてください');
        }

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return redirect($request->input('redirect_to', '/mypage'))->with('success', 'プロフィールを更新しました');
    }
}
