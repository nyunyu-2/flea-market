<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function 商品詳細ページに必要な情報が表示される()
    {
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 12345,
            'description' => '商品の説明文です。',
            'status' => 'available',
        ]);

        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id')->toArray());

        Like::factory()->count(3)->create(['item_id' => $item->id]);

        $commentUsers = User::factory()->count(2)->create();
        foreach ($commentUsers as $commentUser) {
            Comment::factory()->create([
                'item_id' => $item->id,
                'user_id' => $commentUser->id,
                'body' => 'コメントの内容',
            ]);
        }

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('12,345');
        $response->assertSee('商品の説明文です。');
        $response->assertSee('available');

        $response->assertSee('3');

        $response->assertSee('2');

        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }

        foreach ($commentUsers as $commentUser) {
            $response->assertSee($commentUser->name);
            $response->assertSee('コメントの内容');
        }
    }

    /** @test */
    public function ユーザーは商品にいいねをつけることができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('items.like', $item->id));

        $response->assertStatus(302);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function いいね済みの商品はアイコンの色が変わる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('items.show', $item->id));

        $response->assertSee('liked');
    }

    /** @test */
    public function ユーザーはいいね済みの商品に対していいねを解除できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete(route('items.unlike', $item->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/comments', [
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);

        $response->assertRedirect(); // 通常はリダイレクトされる
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'body' => 'テストコメント',
        ]);
    }

    /** @test */
    public function ログインしていないユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post('/comments', [
            'item_id' => $item->id,
            'body' => '未ログインコメント',
        ]);

        $response->assertRedirect('/login'); // middleware('auth') の動作
        $this->assertDatabaseMissing('comments', [
            'body' => '未ログインコメント',
        ]);
    }

    /** @test */
    public function コメントが空の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->from(route('items.show', $item->id))
                        ->post('/comments', [
                            'item_id' => $item->id,
                            'body' => '',
                        ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors('body');
    }

    /** @test */
    public function コメントが255字以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $longComment = str_repeat('あ', 256);

        $response = $this->from(route('items.show', $item->id))
                        ->post('/comments', [
                            'item_id' => $item->id,
                            'body' => $longComment,
                        ]);

        $response->assertRedirect(route('items.show', $item->id));
        $response->assertSessionHasErrors('body');
    }

}
