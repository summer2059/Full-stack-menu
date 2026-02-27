<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuCategoryRequest;
use App\Services\Menu\MenuCategoryService;
use Illuminate\Support\Facades\Log;

class MenuCategoryController extends Controller
{
    protected MenuCategoryService $menuCategoryService;

    public function __construct(MenuCategoryService $menuCategoryService)
    {
        $this->menuCategoryService = $menuCategoryService;
    }

    public function index()
    {
        return view('dashboard.menu-category.index');
    }

    public function create()
    {
        return view('dashboard.menu-category.create');
    }

    public function store(MenuCategoryRequest $request)
    {
        try {
            $this->menuCategoryService->create( $request->validated(), $request->hasFile('image') ? $request->file('image') : null );

            toast('Menu Category Added!', 'success');
            return redirect()->route('menu-category.index');
        } catch (\Exception $e) {
            Log::error('MenuCategory store error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to add menu category.', 'error');
            return back()->withInput();
        }
    }

    public function edit(string $id)
    {
        try {
            $category = $this->menuCategoryService->findById((int) $id);
            return view('dashboard.menu-category.edit', compact('category'));
        } catch (\Exception $e) {
            Log::error('MenuCategory edit error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to load menu category.', 'error');
            return redirect()->route('menu-category.index');
        }
    }

    public function update(MenuCategoryRequest $request, string $id)
    {
        try {
            $this->menuCategoryService->update( (int) $id, $request->validated(), $request->hasFile('image') ? $request->file('image') : null );

            toast('Menu Category Updated!', 'success');
            return redirect()->route('menu-category.index');
        } catch (\Exception $e) {
            Log::error('MenuCategory update error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to update menu category.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->menuCategoryService->delete((int) $id);
            toast('Menu Category Deleted!', 'success');
        } catch (\Exception $e) {
            Log::error('MenuCategory delete error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to delete menu category.', 'error');
        }

        return redirect()->route('menu-category.index');
    }
}