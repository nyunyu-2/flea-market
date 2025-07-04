<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class ItemDetailController extends Controller
{
    public function store(CommentRequest $request)
    {
        Comment::create([
            'user_id' => auth()->id(),
            'item_id' => $request->item_id,
            'body' => $request->body,
        ]);

        return back();
    }

    public function show(Item $item)
    {
        $comments = $item->comments()->with('user')->latest()->get();

        return view('items.show', compact('item', 'comments'));
    }

    // ⭐ いいね登録
    public function like(Item $item)
    {
        $item->likes()->create([
            'user_id' => auth()->check() ? auth()->id() : 1,
        ]);

        return back();
    }

    // 💔 いいね解除
    public function unlike(Item $item)
    {
        $item->likes()->where('user_id', auth()->check() ? auth()->id() : 1)->delete();

        return back();
    }
}
