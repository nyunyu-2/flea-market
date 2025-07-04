<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommendation');
        $keyword = $request->query('keyword');

        if ($page === 'mylist') {
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            $user = auth()->user();

            $query = Item::whereHas('likes', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('user_id', '!=', $user->id);

            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }

            $items = $query->get();

        } else {
            $query = Item::latest();

            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }

            $items = $query->get();
        }

        return view('items.index', compact('items', 'page', 'keyword'));
    }

    public function show(Item $item)
    {
        $likeCount = $item->likes()->count();

        $commentCount = $item->comments()->count();

        $comments = $item->comments()->with('user')->get();

        $categories = $item->categories;

        return view('items.show', compact('item', 'likeCount', 'commentCount', 'comments', 'categories'));
    }


    public function create()
    {
        $categories = Category::all();

        return view('sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => auth()->id(),
            'image_path' => $path,
            'status' => $validated['status'],
            'name' => $validated['name'],
            'brand' => $validated['brand'],
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        $item->categories()->attach($validated['categories']);

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }
}
