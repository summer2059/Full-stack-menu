<?php

namespace App\Livewire\Dashboard\Inventory;

use App\Models\InventoryItems;
use App\Models\InventoryLog;
use App\Services\InventoryService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    public ?int    $itemId        = null;
    public string  $name          = '';
    public string  $unit          = '';
    public string  $current_stock = '0';
    public string  $minimum_stock = '0';
    public string  $cost_per_unit = '0';
    public int     $status        = 1;

    // Computed stock value preview (reactive)
    public float   $stockValuePreview = 0.0;

    protected InventoryService $inventoryService;

    public function boot(InventoryService $inventoryService): void
    {
        $this->inventoryService = $inventoryService;
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $item = InventoryItems::findOrFail($id);

            $this->itemId        = $item->id;
            $this->name          = $item->name          ?? '';
            $this->unit          = $item->unit          ?? '';
            $this->current_stock = (string) ($item->current_stock ?? 0);
            $this->minimum_stock = (string) ($item->minimum_stock ?? 0);
            $this->cost_per_unit = (string) ($item->cost_per_unit ?? 0);
            $this->status        = $item->status        ?? 1;

            $this->recalcPreview();
        }
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'status'        => 'required|in:0,1',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'          => 'Item name is required.',
            'unit.required'          => 'Please select a unit.',
            'current_stock.required' => 'Opening stock is required.',
            'current_stock.numeric'  => 'Stock must be a number.',
            'current_stock.min'      => 'Stock cannot be negative.',
        ];
    }

    /**
     * Real-time validation.
     */
    public function updated(string $field): void
    {
        $this->validateOnly($field);
        $this->recalcPreview();
    }

    /**
     * Recalculate stock value preview.
     */
    public function recalcPreview(): void
    {
        $stock = (float) $this->current_stock;
        $cost  = (float) $this->cost_per_unit;
        $this->stockValuePreview = $stock * $cost;
    }

    /**
     * Save — create or update.
     */
    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'name'          => $this->name,
                'unit'          => $this->unit,
                'current_stock' => (float) $this->current_stock,
                'minimum_stock' => (float) $this->minimum_stock,
                'cost_per_unit' => (float) $this->cost_per_unit,
                'status'        => $this->status,
            ];

            if ($this->itemId) {
                // Update
                $item = InventoryItems::findOrFail($this->itemId);
                $item->update($data);
                session()->flash('success', 'Inventory item updated successfully!');
            } else {
                // Create
                $item = InventoryItems::create($data);

                // Log opening stock
                if ($item->current_stock > 0) {
                    InventoryLog::create([
                        'inventory_item_id' => $item->id,
                        'type'              => 'restock',
                        'quantity'          => $item->current_stock,
                        'stock_after'       => $item->current_stock,
                        'note'              => 'Opening stock',
                    ]);
                }

                session()->flash('success', 'Inventory item added successfully!');
            }

            $this->redirect(route('inventory.index'), navigate: true);

        } catch (\Exception $e) {
            Log::error('Inventory Form save error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        // maps to: resources/views/livewire/dashboard/inventory/form.blade.php
        return view('livewire.dashboard.inventory.form');
    }
}