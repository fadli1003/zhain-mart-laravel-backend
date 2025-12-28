<?php

namespace App\Services;

use App\Repositories\WarehouseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WarehouseService
{
   private $warehouse_repo;
    public function __construct(WarehouseRepository $warehouse_repository)
    {
        $this->warehouse_repo = $warehouse_repository;
    }
    public function getAll(array $fields)
    {
        $this->warehouse_repo->getAll($fields);
    }
    public function getById($id, array $fields)
    {
        return $this->warehouse_repo->getById($id, $fields ?? ['*']);
    }
    public function create(array $data)
    {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->warehouse_repo->create($data);
    }
    public function update($id, array $data)
    {
        $fields = ['id', 'photo'];
        $warehouse = $this->warehouse_repo->getById($id, $fields);

        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($warehouse->photo)){
                $this->deletePhoto($warehouse->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->warehouse_repo->update($id, $data);
    }
    public function delete(int $id)
    {
        $fields = ['id', 'photo'];
        $warehouse = $this->warehouse_repo->getById($id, $fields);
        if($warehouse->photo){
            $this->deletePhoto($warehouse->photo);
        }
        $this->warehouse_repo->delete($id);
    }

    public function attachProduct(int $warehouseId, int $productId, int $stock)
    {
        $warehouse = $this->warehouse_repo->getById($warehouseId, ['id']);
        $warehouse->products()->syncWithoutDetaching([
            $productId => ['stock' => $stock],
        ]);
    }
    public function detachProduct(int $warehouseId, int $productId)
    {
        $warehouse = $this->warehouse_repo->getById($warehouseId, ['id']);
        $warehouse->products()->detach($productId);
    }
    public function updateProductStock($warehouseId, $productId, $stock)
    {
        $warehouse = $this->warehouse_repo->getById($warehouseId, ['id']);
        $warehouse->products()->updateExistingPivot($productId, [
            'stock' => $stock,
        ]);

        return $warehouse->products()->where('product_id', $productId)->first();
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('warehouses', 'public');
    }
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'warehouses/'.basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}
