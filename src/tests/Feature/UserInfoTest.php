<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザー情報取得()
    {
        $user = User::factory()->create([
            'username' => 'テスト太郎'. uniqid(),
            'profile_image' => 'test.jpg',
        ]);

        $this->actingAs($user);

        $item1 = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品1',
            'status' => 'available',
            'image_path' => 'items/sample.jpg',
        ]);

        $item2 = Item::factory()->create([
            'name' => '購入商品1',
            'status' => 'sold',
            'image_path' => 'items/sample.jpg',
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);

        $response = $this->get('/mypage?page=sell');
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('test.jpg');
        $response->assertSee('出品商品1');

        $response = $this->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('test.jpg');
        $response->assertSee('購入商品1');


    }

    /** @test */
    public function ユーザー情報変更()
    {
        $user = User::factory()->create([
            'username' => 'テスト太郎',
            'profile_image' => 'profile/test.jpg',
            'zipcode' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'building' => 'テストビル101',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.profile'));

        $response->assertStatus(200);
        $response->assertSee('value="テスト太郎"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('東京都千代田区1-1-1', false);
        $response->assertSee('value="テストビル101"', false);
        $response->assertSee(asset('storage/profile/test.jpg'));
    }
}
