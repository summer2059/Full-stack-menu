<?php
// app/Http/Controllers/Dashboard/RecipeController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InventoryItems;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $menus = Menu::with('inventoryItems')->get();

            return datatables()->of($menus)
                ->addIndexColumn()
                ->addColumn('category', fn($m) => $m->menuCategory->title ?? '—')
                ->addColumn('ingredients_count', fn($m) => $m->inventoryItems->count() . ' ingredients')
                ->addColumn('action', fn($m) =>
                    '<a href="' . route('recipe.edit', $m->id) . '" class="btn btn-sm btn-warning">
                        <i class="fas fa-mortar-pestle"></i> Set Recipe
                    </a>'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.recipe.index');
    }

    public function edit($menu_id)
    {
        $menu           = Menu::with('inventoryItems')->findOrFail($menu_id);
        $inventoryItems = InventoryItems::where('status', 1)->orderBy('name')->get();

        return view('dashboard.recipe.edit', compact('menu', 'inventoryItems'));
    }

    public function update(Request $request, $menu_id)
    {
        $request->validate([
            'ingredients'            => 'required|array|min:1',
            'ingredients.*.item_id'  => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.001',
        ]);

        try {
            $menu = Menu::findOrFail($menu_id);

            $sync = [];
            foreach ($request->ingredients as $ingredient) {
                $sync[$ingredient['item_id']] = [
                    'quantity_required' => $ingredient['quantity'],
                ];
            }

            $menu->inventoryItems()->sync($sync);

            toast('Recipe saved for ' . $menu->title . '!', 'success');
            return redirect()->route('recipe.index');
        } catch (\Exception $e) {
            Log::error('Recipe update error: ' . $e->getMessage());
            toast('Failed to save recipe.', 'error');
            return back()->withInput();
        }
    }
}