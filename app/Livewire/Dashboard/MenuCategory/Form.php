<?php

namespace App\Livewire\Dashboard\MenuCategory;

use App\Services\Menu\MenuCategoryService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    use WithFileUploads;

    public ?int   $categoryId    = null;
    public string $title         = '';
    public string $description   = '';
    public $image;
    public string $existingImage = '';
    public int    $priority      = 0;
    public int    $status        = 1;

    protected MenuCategoryService $menuCategoryService;

    public function boot(MenuCategoryService $menuCategoryService): void
    {
        $this->menuCategoryService = $menuCategoryService;
    }

    public function mount(?int $id = null): void
    {
        if ($id) {
            $category = app(MenuCategoryService::class)->findById($id);

            $this->categoryId    = $category->id;
            $this->title         = $category->title       ?? '';
            $this->description   = $category->description ?? '';
            $this->existingImage = $category->image       ?? '';
            $this->priority      = $category->priority    ?? 0;
            $this->status        = $category->status      ?? 1;
        }
    }

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => $this->categoryId
                                ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                                : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'priority'    => 'required|integer',
            'status'      => 'required|in:0,1',
        ];
    }
    protected function messages(): array
    {
        return [
            'title.required'    => 'Title is required.',
            'image.required'    => 'Please upload an image.',
            'image.image'       => 'The file must be an image.',
            'priority.required' => 'Priority is required.',
            'priority.integer'  => 'Priority must be a valid number.',
            'status.in'         => 'Status must be Active or Inactive.',
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
                'title'       => $this->title,
                'description' => $this->description,
                'priority'    => $this->priority,
                'status'      => $this->status,
            ];
            $image = $this->image ?? null;

            if ($this->categoryId) {
                $this->menuCategoryService->update($this->categoryId, $data, $image);
                session()->flash('success', 'Menu Category Updated Successfully!');
            } else {
                $this->menuCategoryService->create($data, $image);
                session()->flash('success', 'Menu Category Created Successfully!');
            }

            $this->redirect(route('menu-category.index'), navigate: true);

        } catch (\Exception $e) {
            Log::error('Livewire MenuCategory\\Form save error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', 'Something went wrong. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.menu-category.form');
    }
}