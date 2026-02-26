<?php

namespace App\Livewire\Dashboard\Menu;

use App\Models\MenuCategory;
use App\Services\Menu\MenuService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    use WithFileUploads;

    public ?int    $menuId         = null;
    public string  $title          = '';
    public string  $description    = '';
    public $image;
    public string  $existingImage  = '';
    public string  $menu_category_id = '';
    public string  $price          = '';
    public string  $rating         = '';
    public int     $priority       = 0;
    public int     $status         = 1;

    protected MenuService $menuService;

    public function boot(MenuService $menuService): void
    {
        $this->menuService = $menuService;
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $menu = app(MenuService::class)->findById($id);

            $this->menuId           = $menu->id;
            $this->title            = $menu->title            ?? '';
            $this->description      = $menu->description      ?? '';
            $this->existingImage    = $menu->image            ?? '';
            $this->menu_category_id = (string) ($menu->menu_category_id ?? '');
            $this->price            = (string) ($menu->price  ?? '');
            $this->rating           = (string) ($menu->rating ?? '');
            $this->priority         = $menu->priority         ?? 0;
            $this->status           = $menu->status           ?? 1;
        }
    }

    protected function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'description'      => 'nullable|string',
            'image'            => $this->menuId
                                    ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                                    : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'            => 'required|numeric|min:0',
            'rating'           => 'required|numeric|min:1|max:5',
            'priority'         => 'required|integer',
            'status'           => 'required|in:0,1',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'            => 'Title is required.',
            'menu_category_id.required' => 'Please select a category.',
            'menu_category_id.exists'   => 'Selected category does not exist.',
            'image.required'            => 'Please upload an image.',
            'price.required'            => 'Price is required.',
            'rating.required'           => 'Rating is required.',
            'rating.min'                => 'Rating must be at least 1.',
            'rating.max'                => 'Rating cannot exceed 5.',
            'priority.required'         => 'Priority is required.',
        ];
    }

    public function updated(string $field): void
    {
        $this->validateOnly($field);
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'title'            => $this->title,
                'description'      => $this->description,
                'menu_category_id' => $this->menu_category_id,
                'price'            => $this->price,
                'rating'           => $this->rating,
                'priority'         => $this->priority,
                'status'           => $this->status,
            ];

            $image = $this->image ?? null;

            if ($this->menuId) {
                $this->menuService->update($this->menuId, $data, $image);
                session()->flash('success', 'Menu Updated Successfully!');
            } else {
                $this->menuService->create($data, $image);
                session()->flash('success', 'Menu Created Successfully!');
            }

            $this->redirect(route('menu.index'), navigate: true);

        } catch (\Exception $e) {
            Log::error('Livewire Menu\\Form save error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        $categories = MenuCategory::orderBy('title', 'asc')->get();
        return view('livewire.dashboard.menu.form', compact('categories'));
    }
}