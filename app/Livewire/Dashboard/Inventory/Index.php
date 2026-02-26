<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\InventoryItems;
use App\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $sortField     = 'name';
    public string $sortDir       = 'asc';
    public int    $perPage       = 10;
    public string $statusFilter  = '';    // '' = All, '1' = Active, '0' = Inactive
    public string $stockFilter   = '';    // '' = All, 'low' = Low stock, 'out' = Out of stock

    // Restock modal
    public bool   $showRestock   = false;
    public ?int   $restockItemId = null;
    public string $restockName   = '';
    public string $restockUnit   = '';
    public string $restockQty    = '';
    public string $restockNote   = '';

    // Delete modal
    public ?int   $deleteId      = null;
    public bool   $showConfirm   = false;

    protected InventoryService $inventoryService;

    public function boot(InventoryService $inventoryService): void
    {
        $this->inventoryService = $inventoryService;
    }

    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedPerPage(): void     { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedStockFilter(): void  { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        $this->sortDir = $this->sortField === $field
            ? ($this->sortDir === 'asc' ? 'desc' : 'asc')
            : 'asc';
        $this->sortField = $field;
        $this->resetPage();
    }

    /**
     * Toggle status — pure Livewire.
     */
    public function toggleStatus(int $id): void
    {
        try {
            $item      = InventoryItems::findOrFail($id);
            $item->status = $item->status == 1 ? 0 : 1;
            $item->save();
        } catch (\Exception $e) {
            Log::error('Inventory toggleStatus error: ' . $e->getMessage());
            session()->flash('error', 'Failed to update status.');
        }
    }

    /**
     * Open restock modal.
     */
    public function openRestock(int $id, string $name, string $unit): void
    {
        $this->restockItemId = $id;
        $this->restockName   = $name;
        $this->restockUnit   = $unit;
        $this->restockQty    = '';
        $this->restockNote   = '';
        $this->showRestock   = true;
    }

    /**
     * Close restock modal.
     */
    public function closeRestock(): void
    {
        $this->showRestock   = false;
        $this->restockItemId = null;
        $this->restockQty    = '';
        $this->restockNote   = '';
    }

    /**
     * Confirm restock — pure Livewire.
     */
    public function confirmRestock(): void
    {
        $this->validate([
            'restockQty' => 'required|numeric|min:0.01',
        ], [
            'restockQty.required' => 'Quantity is required.',
            'restockQty.min'      => 'Quantity must be greater than 0.',
        ]);

        try {
            $this->inventoryService->restock(
                $this->restockItemId,
                (float) $this->restockQty,
                $this->restockNote ?: 'Manual restock'
            );
            session()->flash('success', 'Stock restocked successfully!');
        } catch (\Exception $e) {
            Log::error('Restock error: ' . $e->getMessage());
            session()->flash('error', 'Failed to restock.');
        }

        $this->closeRestock();
    }

    /**
     * Open delete confirmation modal.
     */
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

    /**
     * Execute delete.
     */
    public function delete(): void
    {
        try {
            if ($this->deleteId) {
                InventoryItems::findOrFail($this->deleteId)->delete();
                session()->flash('success', 'Inventory item deleted!');
            }
        } catch (\Exception $e) {
            Log::error('Inventory delete error: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete item.');
        } finally {
            $this->deleteId    = null;
            $this->showConfirm = false;
        }
    }

    public function render()
    {
        $items = InventoryItems::query()
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->when($this->statusFilter !== '', fn ($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->when($this->stockFilter === 'out', fn ($q) =>
                $q->where('current_stock', '<=', 0)
            )
            ->when($this->stockFilter === 'low', fn ($q) =>
                $q->whereRaw('current_stock > 0 AND current_stock <= minimum_stock')
            )
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.dashboard.inventory.index', compact('items'));
    }
}