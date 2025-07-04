<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーは購入できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);
    }

    /** @test */
    public function 購入済み商品は一覧でSOLDと表示される()
    {
        $item = Item::factory()->create(['is_sold' => true]);

        $response = $this->get('/');

        $response->assertSee('SOLD');
    }

    /** @test */
    public function 購入した商品はマイページに表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create(['status' => 'sold']);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/mypage?page=buy');

        $response->assertSee($item->name);
    }

    /** @test */
    public function 購入時に送付先住所が登録される()
    {
        $user = User::factory()->create([
            'zipcode' => '123-4567',
            'address' => '東京都港区',
            'building' => 'テストビル301',
        ]);

        $item = Item::factory()->create(['status' => 'available']);

        $this->actingAs($user);

        $response = $this->get(route('purchase.success', ['item_id' => $item->id]));

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'zipcode' => '123-4567',
            'address' => '東京都港区',
            'building' => 'テストビル301',
        ]);
    }

}

