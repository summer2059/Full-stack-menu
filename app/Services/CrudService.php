<?php

namespace App\Services;

class CrudService{
    public function create($modelName, $data){
        $modelClass = "App\\Models\\$modelName";

        if(!class_exists($modelClass)){
            throw new \InvalidArgumentException("Model $modelClass not found.");
        }

        if(isset($data['title'])){
            $data['slug'] = $this->generateSlug($data['title'], $modelClass);
        }

        if(isset($data['image'])){
            $data['image'] = $this->uploadFile($data['image'], 'uploads/images');
        }

        return $modelClass::create($data);
    }

    public function update($modelName, $id, $data){
        $modelClass = "App\\Models\\$modelName";
        $model = $modelClass::findOrFail($id);

        if (isset($data['image'])){
            $this->deleteFileIfExists('uploads/images/' .$model->image);
            $data['image'] = $this->uploadFile($data['image'], 'uploads/images');
        }

        // if(isset($data['images']) && is_array($data['images'])){
        //     $data['images'] = [];
        //     foreach ($data['images'] as $image) {
        //         $data['images'][] = $this->uploadImage($image);
        //     }
        // }
        if(isset($data['images']) && is_array($data['images'])){
            $data['images'] = array_map(fn($image) => $this->uploadFile($image, 'uploads/images'), $data['images']);
        }

        if(isset($data['video'])){
            $data['video'] = $data['video'];
        }

        if(isset($data['pdf'])){
            $this->deleteFileIfExists('uploads/pdfs/' . $model->pdf);
            $pdfDetails = $this->uploadFile($data['pdf'], 'uploads/pdfs', true);
            $data['pdf'] = $pdfDetails['name'];
            $data['pdf_size'] = $pdfDetails['size'];
        }

        $model->fill($data)->save();

        return $model;
    }

    public function delete($modelName, $id){
        $modelClass = "App\\Models\\$modelName";
        $model = $modelClass::findOrFail($id);

        // if (isset($model->image)){
        //     $this->deleteImageIfExists($model->image);
        // }

        // if($model->pdf){
        //     $this->deletePdfIfExists($model->pdf);
        // }
        $this->deleteFileIfExists('uploads/images/' . $model->image);
        $this->deleteFileIfExists('uploads/pdfs/' . $model->pdf);
        
        $model->delete();

        return true;
    }

    public function find($modelName, $id){
        $modelClass = "App\\Models\\$modelName";
        return $modelClass::findOrFail($id);

    }

    public function all($modelName){
        $modelClass = "App\\Models\\$modelName";
        return $modelClass::latest()->get();
    }

    public function uploadFile($file, $destinationPath, $isPdf=false){
        $fileName = uniqid(date('YmdHis') . "_", true). "." . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);

        if($isPdf){
            $pdfSize = round(filesize($destinationPath . '/' . $fileName) / 1024, 2);
            return ['name' => $fileName, 'size' => $pdfSize];
        }
        return $fileName;
    }

    // public function uploadImage($image){
    //     $destinationPath = 'uploads/images/';
    //     $imageName = uniqid(date('YmdHis') . "_", true) . "." . $image->getClientOriginalExtension();
    //     $image->move($destinationPath, $imageName);
    //     return $imageName;
    // }

    // public function uploadPdf($pdf){
    //     $destinationPath = 'uploads/pdfs/';
    //     $pdfName = date('ymdHis') . "." . $pdf->getClientOriginalExtension();
    //     $pdf->move($destinationPath, $pdfName);

    //     $pdfSize = round(filesize($destinationPath . $pdfName) / 1024, 2);

    //     return['name' => $pdfName, 'size' => $pdfSize];
    // }

    // public function deleteImageIfExists($imageName){
    //     if($imageName){
    //         $imagePath = 'uploads/images/' . $imageName;
    //         if(file_exists($imagePath)){
    //             unlink($imagePath);
    //         }
    //     }
    // }

    // public function deletePdfIfExists($pdfName){
    //     if($pdfName){
    //         $pdfPath = 'uploads/pdfs/' . $pdfName;
    //         if(file_exists($pdfPath)){
    //             unlink($pdfPath);
    //         }
    //     }
    // }

    public function deleteFileIfExists($filePath){
        if(file_exists($filePath)){
            unlink($filePath);
        }
    }

    public function generateSlug($title, $modelClass){
        $slug = $this->str_slug($title);
        $count = 1;
        while($modelClass::where('slug', $slug)->exists()){
            $slug = $this->str_slug($title) . '-' . $count;
            $count++;
        }
    }

    public function str_slug($title, $seprator = '-'){
        $title = preg_replace('/[^\x20-\x7E]/u', '', $title);
        $title = strtolower($title);
        $title = preg_replace('/\s+/', $seprator, $title);
        return trim($title, $seprator);

    }
    public function toggleStatus($modelName, $id)
    {
        $modelClass = "App\\Models\\$modelName";
        $model = $modelClass::findOrFail($id);
        $model->status = !$model->status;
        $model->save();

        return $model;
    }
}