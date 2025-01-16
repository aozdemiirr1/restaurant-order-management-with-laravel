<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Döner',
                'description' => 'Lezzetli döner çeşitleri',
                'is_active' => true
            ],
            [
                'name' => 'İçecekler',
                'description' => 'Soğuk ve sıcak içecekler',
                'is_active' => true
            ],
            [
                'name' => 'Tatlılar',
                'description' => 'Tatlı çeşitleri',
                'is_active' => true
            ],
            [
                'name' => 'Ciğerler',
                'description' => 'Ciğer çeşitleri',
                'is_active' => true
            ],
            [
                'name' => 'Kırmızı Et',
                'description' => 'Kırmızı et çeşitleri',
                'is_active' => true
            ],
            [
                'name' => 'Yan Ürünler',
                'description' => 'Çorbalar, salatalar ve diğer yan ürünler',
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
