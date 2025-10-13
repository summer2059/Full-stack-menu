<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        $menuItems = [
            (object)[ 'name' => 'Cheese Burger', 'category' => 'food', 'price' => 5.99, 'image' => 'https://source.unsplash.com/400x300/?burger' ],
            (object)[ 'name' => 'Veg Pizza', 'category' => 'food', 'price' => 7.49, 'image' => 'https://source.unsplash.com/400x300/?pizza' ],
            (object)[ 'name' => 'Italian Pasta', 'category' => 'food', 'price' => 6.20, 'image' => 'https://source.unsplash.com/400x300/?pasta' ],
            (object)[ 'name' => 'Cappuccino', 'category' => 'drink', 'price' => 3.50, 'image' => 'https://source.unsplash.com/400x300/?coffee' ],
            (object)[ 'name' => 'Fresh Orange Juice', 'category' => 'drink', 'price' => 2.80, 'image' => 'https://source.unsplash.com/400x300/?juice' ],
        ];

        return view('frontend.index', compact('menuItems'));
    }
}
