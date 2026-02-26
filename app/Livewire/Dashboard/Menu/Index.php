<?php

namespace App\Livewire\Dashboard\Menu;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Services\Menu\MenuService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $sortField      = 'created_at';
    public string $sortDir        = 'desc';
    public int    $perPage        = 10;
    public string $categoryFilter = '';   
    public ?int   $deleteId       = null;
    public bool   $showConfirm    = false;

    protected MenuService $menuService;

    public function boot(MenuService $menuService): void
    {
        $this->menuService = $menuService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir   = 'asc';
        }
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        try {
            $menu      = $this->menuService->findById($id);
            $newStatus = $menu->status == 1 ? 0 : 1;
            $this->menuService->update($id, ['status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('Menu toggleStatus error: ' . $e->getMessage());
            session()->flash('error', 'Failed to update status.');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId    = $id;
        $this->showConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->deleteId    = null;
        $this->showConfirm = false;
    }

    public function delete(): void
    {
        try {
            if ($this->deleteId) {
                $this->menuService->delete($this->deleteId);
                session()->flash('success', 'Menu Deleted Successfully!');
            }
        } catch (\Exception $e) {
            Log::error('Menu delete error: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete menu.');
        } finally {
            $this->deleteId    = null;
            $this->showConfirm = false;
        }
    }

    public function render()
    {
        $menus = Menu::with('menuCategory')
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('menuCategory', fn ($q2) =>
                      $q2->where('title', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->categoryFilter !== '', fn ($q) =>
                $q->where('menu_category_id', $this->categoryFilter)
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        $categories = MenuCategory::orderBy('title', 'asc')->get();
        return view('livewire.dashboard.menu.index', compact('menus', 'categories'));
    }
}