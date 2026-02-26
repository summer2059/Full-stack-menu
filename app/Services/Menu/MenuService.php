<?php

namespace App\Services\Menu;

use App\Models\Menu;
use Illuminate\Http\UploadedFile;

class MenuService
{
    public function getAll()
    {
        return Menu::with('menuCategory')->latest()->get();
    }

    public function findById(int $id): Menu
    {
        return Menu::with('menuCategory')->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): Menu
    {
        if ($image) {
            $data['image'] = $this->uploadImage($image);
        }

        if (array_key_exists('status', $data)) {
            $data['status'] = (int) $data['status'];
        }

        return Menu::create($data);
    }

    public function update(int $id, array $data, ?UploadedFile $image = null): Menu
    {
        $menu = $this->findById($id);

        if ($image) {
            $this->deleteOldImage($menu->image);
            $data['image'] = $this->uploadImage($image);
        }

        if (array_key_exists('status', $data)) {
            $data['status'] = (int) $data['status'];
        }

        $menu->update($data);

        return $menu->fresh();
    }

    public function delete(int $id): void
    {
        $menu = $this->findById($id);
        $this->deleteOldImage($menu->image);
        $menu->delete();
    }
    private function uploadImage(UploadedFile $image): string
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $destPath = public_path('uploads/images');

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