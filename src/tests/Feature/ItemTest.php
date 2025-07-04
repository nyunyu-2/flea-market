<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Like;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('items', function ($items) {
            return $items->count() === 3;
        });
    }

    /** @test */
    public function 購入済み商品にはSoldと表示される()
    {
        $item = Item::factory()->create([
            'is_sold' => true,
        ]);

        $response = $this->get('/');

        $response->assertSee('SOLD');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

        $otherItem = Item::factory()->create([
            'name' => '他人の商品',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }

    /** @test */
    public function マイリストにはいいねした自分以外の商品だけが表示され、購入済み商品にはSOLDラベルが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertTrue(auth()->check());

        $myItem = Item::factory()->create(['user_id' => $user->id]);

        $otherUser1 = User::factory()->create();
        $item1 = Item::factory()->create([
            'user_id' => $otherUser1->id,
            'is_sold' => false,
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);

        $otherUser2 = User::factory()->create();
        $item2 = Item::factory()->create([
            'user_id' => $otherUser2->id,
            'is_sold' => true,
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);

        $response = $this->get('/?page=mylist');

        $response->assertStatus(200);
        $response->assertSee($item1->name);
        $response->assertSee($item2->name);
        $response->assertSee('SOLD');
        $response->assertDontSee($myItem->name);

        auth()->logout();
        $response = $this->get('/?page=mylist');
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function 商品一覧ページで商品名による部分一致検索ができる()
    {
        Item::factory()->create(['name' => 'MacBook Air']);
        Item::factory()->create(['name' => 'iPhone 14']);

        $response = $this->get('/?keyword=MacBook');

        $response->assertSee('MacBook Air');
        $response->assertDontSee('iPhone 14');
    }

    /** @test */
    public function マイリストページで商品名による部分一致検索ができる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item1 = Item::factory()->create(['name' => 'Kindle Paperwhite']);
        $item2 = Item::factory()->create(['name' => 'Amazon Echo']);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $item1->id]);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $item2->id]);

        $response = $this->get('/?page=mylist&keyword=Kindle');

        $response->assertSee('Kindle Paperwhite');
        $response->assertDontSee('Amazon Echo');
    }

    /** @test */
    public function マイリストでも検索キーワードが保持されている()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => Item::factory()->create(['name' => 'iPad'])->id,
        ]);

        $response = $this->get('/?page=mylist&keyword=iPad');

        $response->assertSee('value="iPad"', false);
    }

}
