<?php
namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    private $category_repo;
    public function __construct(CategoryRepository $category_repository)
    {
        $this->category_repo = $category_repository;
    }
    public function getAll(array $fields)
    {
        $this->category_repo->getAll($fields);
    }
    public function getById(int $id, array $fields)
    {
        return $this->category_repo->getById($id, $fields ?? ['*']);
    }
    public function create(array $data)
    {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
    }
    public function update(int $id, array $data)
    {
        $fields = ['id', 'photo'];
        $category = $this->category_repo->getById($id, $fields);

        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($category->photo)){
                $this->deletePhoto($category->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->category_repo->update($id, $data);
    }
    public function delete(int $id)
    {
        $fields = ['id', 'photo'];
        $category = $this->category_repo->getById($id, $fields);
        if($category->photo){
            $this->deletePhoto($category->photo);
        }
        $this->category_repo->delete($id);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('categories', 'public');
    }
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'categories/'.basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}
