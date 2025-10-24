<?php

namespace Database\Seeders\Menu;

use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Food', 'slug' => 'food', 'description' => 'Tasty dishes', 'priority' => 1, 'status' => 1],
            ['title' => 'Drinks', 'slug' => 'drinks', 'description' => 'Refreshing beverages', 'priority' => 2, 'status' => 1],
            ['title' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats', 'priority' => 3, 'status' => 1],
        ];

        foreach ($categories as $category) {
            MenuCategory::create($category);
        }
    }
}
