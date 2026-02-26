<?php

namespace App\Services\Menu;

use App\Models\MenuCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class MenuCategoryService
{
    public function getAll()
    {
        return MenuCategory::latest()->get();
    }

    public function findById(int $id): MenuCategory
    {
        return MenuCategory::findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): MenuCategory
    {
        if ($image) {
            $data['image'] = $this->uploadImage($image);
        }

        if (array_key_exists('status', $data)) {
            $data['status'] = (int) $data['status'];
        }

        return MenuCategory::create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): MenuCategory
    {
        $category = $this->findById($id);

        if ($image) {
            $this->deleteOldImage($category->image);
            $data['image'] = $this->uploadImage($image);
        }

        if (array_key_exists('status', $data)) {
            $data['status'] = (int) $data['status'];
        }

        $category->update($data);

        return $category->fresh();
    }

    public function delete(int $id): void
    {
        $category = $this->findById($id);
        $this->deleteOldImage($category->image);
        $category->delete();
    }

    private function uploadImage(UploadedFile $image): string
    {
        $filename  = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $destPath  = public_path('uploads/images');
        if (! is_dir($destPath)) {
            mkdir($destPath, 0755, true);
        }
        copy($image->getRealPath(), $destPath . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    private function deleteOldImage(?string $filename): void
    {
        if ($filename) {
            $path = public_path('uploads/images/' . $filename);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}