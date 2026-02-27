<?php

namespace Database\Seeders\Menu;

use App\Models\InventoryItems;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Helper: resolve item name → id, cache in a local map.
     */
    private array $itemMap = [];

    private function item(string $name): ?int
    {
        if (!isset($this->itemMap[$name])) {
            $record = InventoryItems::where('name', $name)->first();
            $this->itemMap[$name] = $record?->id;
        }
        return $this->itemMap[$name];
    }

    /**
     * Sync ingredients for a menu identified by slug.
     *
     * @param  string  $slug
     * @param  array<string, float>  $ingredients  [ 'Item Name' => quantity_required ]
     */
    private function setRecipe(string $slug, array $ingredients): void
    {
        $menu = Menu::where('slug', $slug)->first();
        if (!$menu) {
            return;
        }

        $sync = [];
        foreach ($ingredients as $itemName => $qty) {
            $id = $this->item($itemName);
            if ($id) {
                $sync[$id] = ['quantity_required' => $qty];
            }
        }

        $menu->inventoryItems()->sync($sync);
    }

    public function run(): void
    {
        // ── BURGERS ────────────────────────────────────────────────────────

        $this->setRecipe('classic-beef-burger', [
            'Beef Patty'    => 150,
            'Burger Bun'    => 1,
            'Lettuce'       => 30,
            'Tomato'        => 40,
            'Mayonnaise'    => 20,
            'Salt'          => 3,
            'Black Pepper'  => 2,
        ]);

        $this->setRecipe('cheese-burger', [
            'Beef Patty'    => 150,
            'Burger Bun'    => 1,
            'Cheddar Cheese'=> 40,
            'Lettuce'       => 25,
            'Tomato'        => 40,
            'Mayonnaise'    => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('double-smash-burger', [
            'Beef Patty'    => 300,
            'Burger Bun'    => 1,
            'Cheddar Cheese'=> 60,
            'Mayonnaise'    => 25,
            'Onion'         => 20,
            'Lettuce'       => 20,
            'Salt'          => 4,
            'Black Pepper'  => 3,
        ]);

        $this->setRecipe('crispy-chicken-burger', [
            'Chicken Breast'  => 150,
            'Burger Bun'      => 1,
            'Cabbage'         => 50,
            'Mayonnaise'      => 25,
            'Salt'            => 3,
            'Black Pepper'    => 2,
        ]);

        $this->setRecipe('spicy-jalapeno-burger', [
            'Beef Patty'    => 150,
            'Burger Bun'    => 1,
            'Jalapeño'      => 30,
            'Swiss Cheese'  => 40,
            'Sriracha'      => 15,
            'Lettuce'       => 25,
            'Salt'          => 3,
        ]);

        $this->setRecipe('bbq-bacon-burger', [
            'Beef Patty'    => 150,
            'Burger Bun'    => 1,
            'Bacon Strips'  => 50,
            'BBQ Sauce'     => 30,
            'Onion'         => 40,
            'Cheddar Cheese'=> 40,
            'Salt'          => 3,
        ]);

        $this->setRecipe('mushroom-swiss-burger', [
            'Beef Patty'    => 150,
            'Burger Bun'    => 1,
            'Mushrooms'     => 60,
            'Swiss Cheese'  => 40,
            'Butter'        => 15,
            'Salt'          => 3,
        ]);

        $this->setRecipe('veggie-bean-burger', [
            'Chickpeas'     => 100,
            'Burger Bun'    => 1,
            'Avocado'       => 0.5,
            'Lettuce'       => 30,
            'Tomato'        => 40,
            'Mayonnaise'    => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('truffle-burger', [
            'Wagyu Beef'    => 180,
            'Burger Bun'    => 1,
            'Truffle Oil'   => 10,
            'Mayonnaise'    => 20,
            'Rocket (Arugula)' => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('fish-fillet-burger', [
            'Fish Fillet'   => 150,
            'Burger Bun'    => 1,
            'Tartar Sauce'  => 25,
            'Lettuce'       => 25,
            'Salt'          => 3,
        ]);

        // ── PIZZAS ─────────────────────────────────────────────────────────

        $this->setRecipe('margherita-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 120,
            'Fresh Basil'   => 10,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('pepperoni-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 100,
            'Pepperoni'     => 80,
        ]);

        $this->setRecipe('bbq-chicken-pizza', [
            'Pizza Dough'   => 250,
            'BBQ Sauce'     => 80,
            'Chicken Breast'=> 120,
            'Mozzarella'    => 100,
            'Onion'         => 40,
        ]);

        $this->setRecipe('veg-supreme-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 100,
            'Bell Pepper'   => 40,
            'Mushrooms'     => 40,
            'Olives'        => 30,
            'Onion'         => 30,
            'Corn'          => 30,
        ]);

        $this->setRecipe('four-cheese-pizza', [
            'Pizza Dough'   => 250,
            'Mozzarella'    => 80,
            'Cheddar Cheese'=> 40,
            'Parmesan'      => 40,
            'Gorgonzola'    => 40,
            'Olive Oil'     => 10,
        ]);

        $this->setRecipe('hawaiian-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 100,
            'Ham'           => 80,
            'Pineapple'     => 60,
        ]);

        $this->setRecipe('meat-feast-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 100,
            'Pepperoni'     => 50,
            'Sausage'       => 50,
            'Ham'           => 50,
            'Minced Beef'   => 50,
        ]);

        $this->setRecipe('spicy-diavola-pizza', [
            'Pizza Dough'   => 250,
            'Tomato Sauce'  => 80,
            'Mozzarella'    => 100,
            'Pepperoni'     => 80,
            'Chilli Flakes' => 5,
        ]);

        $this->setRecipe('pesto-chicken-pizza', [
            'Pizza Dough'       => 250,
            'Basil Pesto'       => 60,
            'Chicken Breast'    => 100,
            'Sun-Dried Tomatoes'=> 40,
            'Mozzarella'        => 100,
        ]);

        $this->setRecipe('truffle-mushroom-pizza', [
            'Pizza Dough'   => 250,
            'Truffle Oil'   => 20,
            'Mushrooms'     => 100,
            'Parmesan'      => 60,
            'Rocket (Arugula)' => 20,
            'Mozzarella'    => 80,
        ]);

        // ── PASTA ──────────────────────────────────────────────────────────

        $this->setRecipe('spaghetti-bolognese', [
            'Spaghetti'     => 150,
            'Minced Beef'   => 120,
            'Tomato Sauce'  => 100,
            'Onion'         => 40,
            'Garlic'        => 10,
            'Olive Oil'     => 15,
            'Salt'          => 3,
        ]);

        $this->setRecipe('spaghetti-carbonara', [
            'Spaghetti'     => 150,
            'Pancetta'      => 80,
            'Egg'           => 2,
            'Parmesan'      => 40,
            'Black Pepper'  => 3,
            'Salt'          => 2,
        ]);

        $this->setRecipe('penne-arrabbiata', [
            'Penne'         => 150,
            'Tomato Sauce'  => 100,
            'Garlic'        => 10,
            'Chilli'        => 10,
            'Olive Oil'     => 15,
            'Salt'          => 3,
        ]);

        $this->setRecipe('fettuccine-alfredo', [
            'Fettuccine'    => 150,
            'Butter'        => 30,
            'Parmesan'      => 60,
            'Cream'         => 80,
            'Salt'          => 2,
            'Black Pepper'  => 2,
        ]);

        $this->setRecipe('lasagna', [
            'Lasagna Sheets'=> 150,
            'Minced Beef'   => 150,
            'Tomato Sauce'  => 100,
            'Béchamel Sauce'=> 100,
            'Mozzarella'    => 80,
            'Parmesan'      => 30,
            'Onion'         => 40,
        ]);

        $this->setRecipe('pesto-pasta', [
            'Penne'         => 150,
            'Basil Pesto'   => 60,
            'Pine Nuts'     => 20,
            'Parmesan'      => 40,
            'Olive Oil'     => 10,
        ]);

        $this->setRecipe('seafood-linguine', [
            'Linguine'      => 150,
            'Prawns'        => 80,
            'Squid'         => 60,
            'Mussels'       => 60,
            'Tomato Sauce'  => 80,
            'White Wine'    => 40,
            'Garlic'        => 10,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('mushroom-risotto', [
            'Arborio Rice'  => 150,
            'Mushrooms'     => 100,
            'Truffle Oil'   => 10,
            'Parmesan'      => 40,
            'Butter'        => 20,
            'White Wine'    => 40,
            'Salt'          => 3,
        ]);

        $this->setRecipe('chicken-penne', [
            'Penne'         => 150,
            'Chicken Breast'=> 120,
            'Tomato Sauce'  => 80,
            'Cream'         => 60,
            'Garlic'        => 10,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('gnocchi-sorrentina', [
            'Potato Gnocchi'=> 200,
            'Tomato Sauce'  => 100,
            'Mozzarella'    => 80,
            'Fresh Basil'   => 10,
            'Olive Oil'     => 10,
        ]);

        // ── SALADS ─────────────────────────────────────────────────────────

        $this->setRecipe('caesar-salad', [
            'Romaine Lettuce'=> 120,
            'Croutons'       => 30,
            'Parmesan'       => 30,
            'Caesar Dressing'=> 40,
        ]);

        $this->setRecipe('greek-salad', [
            'Tomato'        => 80,
            'Cucumber'      => 60,
            'Olives'        => 30,
            'Feta Cheese'   => 60,
            'Oregano'       => 2,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('garden-fresh-salad', [
            'Lettuce'       => 80,
            'Tomato'        => 60,
            'Cucumber'      => 50,
            'Olive Oil'     => 15,
            'Lemon'         => 0.5,
            'Salt'          => 2,
        ]);

        $this->setRecipe('chicken-avocado-salad', [
            'Chicken Breast'=> 120,
            'Avocado'       => 1,
            'Corn'          => 40,
            'Lettuce'       => 60,
            'Lime'          => 0.5,
            'Olive Oil'     => 10,
        ]);

        $this->setRecipe('nicoise-salad', [
            'Tuna (canned)' => 80,
            'Egg'           => 1,
            'Green Beans'   => 50,
            'Olives'        => 30,
            'Tomato'        => 60,
            'Mustard'       => 10,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('caprese-salad', [
            'Mozzarella'    => 100,
            'Tomato'        => 100,
            'Fresh Basil'   => 10,
            'Olive Oil'     => 15,
            'Salt'          => 2,
        ]);

        $this->setRecipe('quinoa-salad', [
            'Quinoa'        => 100,
            'Chickpeas'     => 60,
            'Carrot'        => 50,
            'Bell Pepper'   => 40,
            'Olive Oil'     => 15,
            'Lemon'         => 0.5,
        ]);

        $this->setRecipe('coleslaw', [
            'Cabbage'       => 100,
            'Carrot'        => 50,
            'Mayonnaise'    => 40,
            'Salt'          => 2,
            'Black Pepper'  => 1,
        ]);

        $this->setRecipe('waldorf-salad', [
            'Apple'         => 80,
            'Celery'        => 40,
            'Walnut'        => 30,
            'Grapes'        => 50,
            'Mayonnaise'    => 30,
        ]);

        $this->setRecipe('prawn-salad', [
            'Prawns'        => 120,
            'Avocado'       => 1,
            'Mango'         => 60,
            'Lettuce'       => 60,
            'Lime'          => 0.5,
            'Olive Oil'     => 10,
        ]);

        // ── SANDWICHES ─────────────────────────────────────────────────────

        $this->setRecipe('club-sandwich', [
            'Chicken Breast'=> 100,
            'Bacon Strips'  => 40,
            'Egg'           => 1,
            'Lettuce'       => 30,
            'Tomato'        => 40,
            'Mayonnaise'    => 25,
            'White Bread'   => 3,
        ]);

        $this->setRecipe('blt-sandwich', [
            'Bacon Strips'  => 60,
            'Lettuce'       => 30,
            'Tomato'        => 60,
            'Mayonnaise'    => 20,
            'Sourdough Bread'=> 2,
        ]);

        $this->setRecipe('grilled-cheese', [
            'Cheddar Cheese'=> 80,
            'White Bread'   => 2,
            'Butter'        => 20,
        ]);

        $this->setRecipe('tuna-melt', [
            'Tuna (canned)' => 80,
            'Cheddar Cheese'=> 40,
            'Mayonnaise'    => 20,
            'Sourdough Bread'=> 2,
        ]);

        $this->setRecipe('chicken-caesar-wrap', [
            'Chicken Breast'    => 120,
            'Romaine Lettuce'   => 60,
            'Parmesan'          => 20,
            'Caesar Dressing'   => 30,
            'Flour Tortilla'    => 1,
        ]);

        $this->setRecipe('falafel-wrap', [
            'Chickpeas'     => 100,
            'Hummus'        => 40,
            'Lettuce'       => 30,
            'Tomato'        => 40,
            'Flatbread'     => 1,
            'Garlic'        => 5,
            'Cumin'         => 3,
        ]);

        $this->setRecipe('beef-sub', [
            'Minced Beef'   => 120,
            'Onion'         => 50,
            'Mustard'       => 15,
            'Hoagie Roll'   => 1,
            'Salt'          => 3,
        ]);

        $this->setRecipe('veggie-panini', [
            'Bell Pepper'   => 60,
            'Mushrooms'     => 50,
            'Basil Pesto'   => 30,
            'Panini Bread'  => 1,
            'Olive Oil'     => 10,
        ]);

        $this->setRecipe('egg-cress-sandwich', [
            'Egg'           => 2,
            'Mayonnaise'    => 20,
            'Mustard'       => 10,
            'White Bread'   => 2,
            'Salt'          => 2,
        ]);

        $this->setRecipe('pulled-pork-sandwich', [
            'Pulled Pork'   => 150,
            'BBQ Sauce'     => 40,
            'Apple Slaw'    => 60,
            'Hoagie Roll'   => 1,
        ]);

        // ── SOUPS ──────────────────────────────────────────────────────────

        $this->setRecipe('tomato-basil-soup', [
            'Tomato'        => 200,
            'Fresh Basil'   => 10,
            'Cream'         => 30,
            'Garlic'        => 8,
            'Olive Oil'     => 15,
            'Salt'          => 3,
        ]);

        $this->setRecipe('cream-mushroom-soup', [
            'Mushrooms'     => 200,
            'Cream'         => 80,
            'Onion'         => 40,
            'Butter'        => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('french-onion-soup', [
            'Onion'         => 250,
            'Gruyère'       => 60,
            'Butter'        => 25,
            'White Wine'    => 40,
            'Croutons'      => 40,
            'Salt'          => 3,
        ]);

        $this->setRecipe('chicken-noodle-soup', [
            'Chicken Breast'=> 100,
            'Egg Noodles'   => 80,
            'Carrot'        => 50,
            'Celery'        => 40,
            'Onion'         => 40,
            'Salt'          => 3,
        ]);

        $this->setRecipe('lentil-soup', [
            'Lentils'       => 150,
            'Onion'         => 50,
            'Garlic'        => 10,
            'Cumin'         => 3,
            'Lemon'         => 0.5,
            'Olive Oil'     => 15,
        ]);

        $this->setRecipe('pumpkin-soup', [
            'Pumpkin'       => 300,
            'Cream'         => 50,
            'Onion'         => 40,
            'Butter'        => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('minestrone', [
            'Tomato'        => 100,
            'Carrot'        => 50,
            'Celery'        => 40,
            'Onion'         => 40,
            'Chickpeas'     => 60,
            'Olive Oil'     => 15,
            'Salt'          => 3,
        ]);

        $this->setRecipe('broccoli-cheddar-soup', [
            'Broccoli'      => 200,
            'Cheddar Cheese'=> 80,
            'Cream'         => 60,
            'Onion'         => 40,
            'Butter'        => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('seafood-chowder', [
            'Fish Fillet'   => 100,
            'Prawns'        => 60,
            'Mussels'       => 60,
            'Cream'         => 100,
            'Onion'         => 40,
            'Butter'        => 20,
            'Salt'          => 3,
        ]);

        $this->setRecipe('gazpacho', [
            'Tomato'        => 200,
            'Cucumber'      => 80,
            'Bell Pepper'   => 60,
            'Garlic'        => 8,
            'Olive Oil'     => 20,
            'Lemon'         => 0.5,
            'Salt'          => 3,
        ]);

        // ── GRILLS ─────────────────────────────────────────────────────────

        $this->setRecipe('ribeye-steak', [
            'Ribeye Steak'  => 300,
            'Butter'        => 30,
            'Garlic'        => 10,
            'Rosemary'      => 5,
            'Salt'          => 5,
            'Black Pepper'  => 3,
        ]);

        $this->setRecipe('grilled-chicken', [
            'Chicken Breast'=> 200,
            'Olive Oil'     => 20,
            'Garlic'        => 8,
            'Lemon'         => 0.5,
            'Rosemary'      => 5,
            'Salt'          => 4,
        ]);

        $this->setRecipe('bbq-pork-ribs', [
            'Pork Ribs'     => 400,
            'BBQ Sauce'     => 80,
            'Salt'          => 5,
            'Black Pepper'  => 3,
            'Honey'         => 20,
        ]);

        $this->setRecipe('lamb-chops', [
            'Lamb Chops'    => 300,
            'Rosemary'      => 8,
            'Garlic'        => 10,
            'Olive Oil'     => 20,
            'Salt'          => 5,
        ]);

        $this->setRecipe('grilled-salmon', [
            'Salmon Fillet' => 200,
            'Butter'        => 25,
            'Lemon'         => 1,
            'Asparagus'     => 80,
            'Salt'          => 4,
        ]);

        $this->setRecipe('chicken-skewers', [
            'Chicken Breast'=> 180,
            'Tzatziki'      => 60,
            'Flatbread'     => 1,
            'Olive Oil'     => 15,
            'Garlic'        => 8,
            'Salt'          => 3,
        ]);

        $this->setRecipe('mixed-grill-platter', [
            'Pork Ribs'     => 200,
            'Chicken Breast'=> 150,
            'Sausage'       => 100,
            'Lamb Chops'    => 150,
            'BBQ Sauce'     => 60,
            'Salt'          => 6,
        ]);

        $this->setRecipe('grilled-prawns', [
            'Prawns'        => 200,
            'Butter'        => 30,
            'Garlic'        => 10,
            'Fresh Basil'   => 5,
            'Salt'          => 3,
        ]);

        $this->setRecipe('t-bone-steak', [
            'T-Bone Steak'  => 400,
            'Butter'        => 30,
            'Garlic'        => 10,
            'Rosemary'      => 5,
            'Salt'          => 5,
            'Black Pepper'  => 3,
        ]);

        $this->setRecipe('corn-on-the-cob', [
            'Corn'          => 200,
            'Butter'        => 20,
            'Chilli'        => 5,
            'Lime'          => 0.5,
            'Salt'          => 3,
        ]);

        // ── DESSERTS ───────────────────────────────────────────────────────

        $this->setRecipe('chocolate-lava-cake', [
            'Dark Chocolate'=> 80,
            'Butter'        => 50,
            'Egg'           => 2,
            'Sugar Syrup'   => 20,
            'Cream'         => 30,
        ]);

        $this->setRecipe('new-york-cheesecake', [
            'Cream'         => 150,
            'Egg'           => 2,
            'Butter'        => 40,
            'Sugar Syrup'   => 40,
            'Raspberry Coulis'=> 30,
        ]);

        $this->setRecipe('tiramisu', [
            'Ladyfingers'   => 100,
            'Mascarpone'    => 120,
            'Egg'           => 2,
            'Coffee (Brewed)'=> 80,
            'Sugar Syrup'   => 20,
        ]);

        $this->setRecipe('creme-brulee', [
            'Cream'         => 150,
            'Egg'           => 3,
            'Vanilla Extract'=> 5,
            'Sugar Syrup'   => 30,
        ]);

        $this->setRecipe('brownie-ice-cream', [
            'Dark Chocolate'=> 80,
            'Butter'        => 50,
            'Egg'           => 2,
            'Vanilla Ice Cream'=> 100,
            'Ganache'       => 30,
        ]);

        $this->setRecipe('waffles', [
            'Waffle Mix'    => 150,
            'Butter'        => 30,
            'Egg'           => 1,
            'Milk'          => 100,
            'Whipped Cream' => 40,
            'Maple Syrup'   => 30,
        ]);

        $this->setRecipe('panna-cotta', [
            'Cream'         => 200,
            'Milk'          => 50,
            'Vanilla Extract'=> 5,
            'Sugar Syrup'   => 25,
            'Raspberry Coulis'=> 30,
        ]);

        $this->setRecipe('apple-crumble', [
            'Apple'         => 200,
            'Oats'          => 60,
            'Butter'        => 40,
            'Sugar Syrup'   => 30,
            'Vanilla Ice Cream'=> 80,
        ]);

        $this->setRecipe('ice-cream-sundae', [
            'Vanilla Ice Cream'=> 200,
            'Chocolate Sauce'=> 40,
            'Walnut'        => 20,
            'Whipped Cream' => 30,
        ]);

        $this->setRecipe('sticky-toffee-pudding', [
            'Egg'           => 2,
            'Butter'        => 60,
            'Milk'          => 60,
            'Toffee Sauce'  => 80,
            'Sugar Syrup'   => 30,
        ]);

        // ── HOT DRINKS ─────────────────────────────────────────────────────

        $this->setRecipe('espresso', [
            'Espresso Shot' => 30,
        ]);

        $this->setRecipe('cappuccino', [
            'Espresso Shot' => 30,
            'Steamed Milk'  => 100,
        ]);

        $this->setRecipe('latte', [
            'Espresso Shot' => 30,
            'Steamed Milk'  => 200,
        ]);

        $this->setRecipe('flat-white', [
            'Espresso Shot' => 60,
            'Steamed Milk'  => 120,
        ]);

        $this->setRecipe('hot-chocolate', [
            'Belgian Chocolate'=> 40,
            'Steamed Milk'     => 200,
            'Whipped Cream'    => 30,
        ]);

        $this->setRecipe('english-breakfast-tea', [
            'Black Tea Leaves'=> 5,
            'Milk'            => 30,
        ]);

        $this->setRecipe('green-tea', [
            'Green Tea Leaves'=> 5,
        ]);

        $this->setRecipe('chai-latte', [
            'Chai Spice Mix'  => 10,
            'Almond Milk'     => 200,
            'Sugar Syrup'     => 15,
        ]);

        $this->setRecipe('mocha', [
            'Espresso Shot'     => 30,
            'Chocolate Sauce'   => 20,
            'Steamed Milk'      => 160,
        ]);

        $this->setRecipe('americano', [
            'Espresso Shot' => 60,
        ]);

        // ── COLD DRINKS ────────────────────────────────────────────────────

        $this->setRecipe('fresh-orange-juice', [
            'Orange'        => 3,
        ]);

        $this->setRecipe('mango-lassi', [
            'Mango'         => 120,
            'Yoghurt'       => 100,
            'Milk'          => 50,
            'Cardamom'      => 1,
            'Sugar Syrup'   => 15,
        ]);

        $this->setRecipe('strawberry-milkshake', [
            'Strawberry'    => 120,
            'Milk'          => 200,
            'Vanilla Ice Cream'=> 80,
            'Whipped Cream' => 30,
        ]);

        $this->setRecipe('iced-latte', [
            'Cold Brew Coffee'=> 60,
            'Milk'            => 150,
        ]);

        $this->setRecipe('watermelon-juice', [
            'Watermelon'    => 300,
            'Lime'          => 0.5,
            'Sugar Syrup'   => 15,
        ]);

        $this->setRecipe('lemonade', [
            'Lemon'         => 2,
            'Sugar Syrup'   => 40,
            'Soda Water'    => 200,
        ]);

        $this->setRecipe('iced-tea', [
            'Black Tea Leaves'=> 5,
            'Lemon'           => 0.5,
            'Mint'            => 5,
            'Sugar Syrup'     => 20,
        ]);

        $this->setRecipe('coconut-water', [
            'Coconut Water' => 350,
        ]);

        $this->setRecipe('berry-smoothie', [
            'Strawberry'    => 60,
            'Mango'         => 60,
            'Almond Milk'   => 200,
            'Honey'         => 15,
        ]);

        $this->setRecipe('virgin-mojito', [
            'Lime'          => 1,
            'Mint'          => 10,
            'Sugar Syrup'   => 25,
            'Soda Water'    => 200,
        ]);
    }
}