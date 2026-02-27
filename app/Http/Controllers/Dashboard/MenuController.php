<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuRequest;
use App\Services\Menu\MenuService;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    protected MenuService $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    public function index()
    {
        return view('dashboard.menu.index');
    }
    public function create()
    {
        return view('dashboard.menu.create');
    }

    public function store(MenuRequest $request)
    {
        try {
            $this->menuService->create( $request->validated(), $request->hasFile('image') ? $request->file('image') : null );

            toast('Menu Added Successfully!', 'success');
            return redirect()->route('menu.index');
        } catch (\Exception $e) {
            Log::error('Menu store error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to add menu. Please try again.', 'error');
            return back()->withInput();
        }
    }

    public function edit(string $id)
    {
        try {
            $menu = $this->menuService->findById((int) $id);
            return view('dashboard.menu.edit', compact('menu'));
        } catch (\Exception $e) {
            Log::error('Menu edit error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Menu not found!', 'error');
            return redirect()->route('menu.index');
        }
    }

    public function update(MenuRequest $request, string $id)
    {
        try {
            $this->menuService->update( (int) $id, $request->validated(), $request->hasFile('image') ? $request->file('image') : null );

            toast('Menu Updated Successfully!', 'success');
            return redirect()->route('menu.index');
        } catch (\Exception $e) {
            Log::error('Menu update error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to update menu. Please try again.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->menuService->delete((int) $id);
            toast('Menu Deleted Successfully!', 'success');
        } catch (\Exception $e) {
            Log::error('Menu delete error: ' . $e->getMessage(), [
                'id'   => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to delete menu. Please try again.', 'error');
        }

        return redirect()->route('menu.index');
    }
}