<?php

namespace App\Livewire\Dashboard\MenuCategory;

use App\Models\MenuCategory;
use App\Services\Menu\MenuCategoryService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $sortField   = 'created_at';
    public string $sortDir     = 'desc';
    public int    $perPage     = 10;
    public ?int   $deleteId    = null;
    public bool   $showConfirm = false;

    protected MenuCategoryService $menuCategoryService;

    public function boot(MenuCategoryService $menuCategoryService): void
    {
        $this->menuCategoryService = $menuCategoryService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
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
            $category  = $this->menuCategoryService->findById($id);
            $newStatus = $category->status == 1 ? 0 : 1;
            $this->menuCategoryService->update($id, ['status' => $newStatus]);
        } catch (\Exception $e) {
            Log::error('MenuCategory toggleStatus error: ' . $e->getMessage());
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
                $this->menuCategoryService->delete($this->deleteId);
                session()->flash('success', 'Menu Category Deleted Successfully!');
            }
        } catch (\Exception $e) {
            Log::error('MenuCategory delete error: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete menu category.');
        } finally {
            $this->deleteId    = null;
            $this->showConfirm = false;
        }
    }

    public function render()
    {
        $categories = MenuCategory::query()
            ->when($this->search, fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.dashboard.menu-category.index', compact('categories'));
    }
}