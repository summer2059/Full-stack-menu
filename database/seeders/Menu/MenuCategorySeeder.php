<?php

namespace Database\Seeders\Menu;

use App\Models\MenuCategory;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['title' => 'Burgers',    'slug' => 'burgers',    'description' => 'Juicy beef, chicken & veggie burgers',   'priority' => 1,  'status' => 1],
            ['title' => 'Pizzas',     'slug' => 'pizzas',     'description' => 'Wood-fired and pan pizzas',               'priority' => 2,  'status' => 1],
            ['title' => 'Pasta',      'slug' => 'pasta',      'description' => 'Italian pasta dishes',                    'priority' => 3,  'status' => 1],
            ['title' => 'Salads',     'slug' => 'salads',     'description' => 'Fresh and healthy salads',                'priority' => 4,  'status' => 1],
            ['title' => 'Sandwiches', 'slug' => 'sandwiches', 'description' => 'Hot and cold sandwiches & wraps',         'priority' => 5,  'status' => 1],
            ['title' => 'Soups',      'slug' => 'soups',      'description' => 'Hot comforting soups',                   'priority' => 6,  'status' => 1],
            ['title' => 'Grills',     'slug' => 'grills',     'description' => 'BBQ and grilled mains',                  'priority' => 7,  'status' => 1],
            ['title' => 'Desserts',   'slug' => 'desserts',   'description' => 'Sweet treats and cakes',                 'priority' => 8,  'status' => 1],
            ['title' => 'Hot Drinks', 'slug' => 'hot-drinks', 'description' => 'Coffee, tea and hot beverages',          'priority' => 9,  'status' => 1],
            ['title' => 'Cold Drinks','slug' => 'cold-drinks','description' => 'Juices, shakes and cold beverages',      'priority' => 10, 'status' => 1],
        ];

        foreach ($categories as $category) {
            MenuCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}