<?php

namespace Database\Seeders\Menu;

use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $food = MenuCategory::where('slug', 'food')->first();
        $drinks = MenuCategory::where('slug', 'drinks')->first();
        $desserts = MenuCategory::where('slug', 'desserts')->first();

        $menus = [
            // Food
            [
                'title' => 'Cheese Burger',
                'slug' => 'cheese-burger',
                'description' => 'A juicy beef patty with melted cheese and fresh toppings.',
                'image' => 'https://source.unsplash.com/400x300/?burger',
                'price' => 599,
                'rating' => 4,
                'priority' => 1,
                'status' => 1,
                'menu_category_id' => $food?->id,
            ],
            [
                'title' => 'Veg Pizza',
                'slug' => 'veg-pizza',
                'description' => 'Loaded with fresh vegetables and mozzarella cheese.',
                'image' => 'https://source.unsplash.com/400x300/?pizza',
                'price' => 749,
                'rating' => 5,
                'priority' => 2,
                'status' => 1,
                'menu_category_id' => $food?->id,
            ],
            // Drinks
            [
                'title' => 'Cappuccino',
                'slug' => 'cappuccino',
                'description' => 'Classic Italian coffee with steamed milk foam.',
                'image' => 'https://source.unsplash.com/400x300/?coffee',
                'price' => 350,
                'rating' => 4,
                'priority' => 1,
                'status' => 1,
                'menu_category_id' => $drinks?->id,
            ],
            [
                'title' => 'Orange Juice',
                'slug' => 'orange-juice',
                'description' => 'Freshly squeezed orange juice.',
                'image' => 'https://source.unsplash.com/400x300/?juice',
                'price' => 280,
                'rating' => 3,
                'priority' => 2,
                'status' => 1,
                'menu_category_id' => $drinks?->id,
            ],
            // Desserts
            [
                'title' => 'Chocolate Cake',
                'slug' => 'chocolate-cake',
                'description' => 'Moist chocolate cake with ganache topping.',
                'image' => 'https://source.unsplash.com/400x300/?chocolate-cake',
                'price' => 450,
                'rating' => 5,
                'priority' => 1,
                'status' => 1,
                'menu_category_id' => $desserts?->id,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
