<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\CategorySeeder;


class SellTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーは商品を出品できる()
    {
        Storage::fake('public');

        $this->seed(CategorySeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);

        $categories = \App\Models\Category::all()->take(2);

        $image = UploadedFile::fake()->create('item.jpg', 500, 'image/jpeg');

        $response = $this->post(route('items.store'), [
            'image' => $image,
            'status' => '良好',
            'name' => 'ダミー商品',
            'brand' => 'ブランド名',
            'description' => '説明文',
            'price' => 1000,
            'categories' => $categories->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['name' => 'ダミー商品']);

        Storage::disk('public')->assertExists('items/' . $image->hashName());
    }
}
