<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::factory()->create();

        $categories = \App\Models\Category::all();

        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'brand' =>'TOKEI',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_path' => 'items/Armani+Mens+Clock.jpg',
                'status' => '良好',
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'brand' =>'ハードディスク',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image_path' => 'items/HDD+Hard+Disk.jpg',
                'status' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'brand' =>'野菜',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_path' => 'items/iLoveIMG+d.jpg',
                'status' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'brand' =>'くつ',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image_path' => 'items/Leather+Shoes+Product+Photo.jpg',
                'status' => '状態が悪い',
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'brand' =>'DELL',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image_path' => 'items/Living+Room+Laptop.jpg',
                'status' => '良好',
            ],
            [
                'user_id' => 2,
                'name' => 'マイク',
                'brand' =>'YAMAHA',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image_path' => 'items/Music+Mic+4632231.jpg',
                'status' => '目立った傷や汚れなし',
            ],
            [
                'user_id' => 2,
                'name' => 'ショルダーバッグ',
                'brand' =>'UNIQLO',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image_path' => 'items/Purse+fashion+pocket.jpg',
                'status' => 'やや傷や汚れあり',
            ],
            [
                'user_id' => 2,
                'name' => 'タンブラー',
                'brand' =>'ニトリ',
                'price' => 2000,
                'description' => '使いやすいタンブラー',
                'image_path' => 'items/Tumbler+souvenir.jpg',
                'status' => '状態が悪い',
            ],
            [
                'user_id' => 2,
                'name' => 'コーヒーミル',
                'brand' =>'ニトリ',
                'price' => 3000,
                'description' => '手動のコーヒーミル',
                'image_path' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'status' => '良好',
            ],
            [
                'user_id' => 2,
                'name' => 'メイクセット',
                'brand' =>'DIOR',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image_path' => 'items/外出メイクアップセット.jpg',
                'status' => '目立った傷や汚れなし',
            ],
        ];

        foreach ($items as $itemData) {
            $item = \App\Models\Item::create($itemData);

            $item->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
