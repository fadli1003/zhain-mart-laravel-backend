<?php
namespace App\Services;

use App\Repositories\MerchantRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MerchantService
{
    private MerchantRepository $merchant_repo;
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchant_repo = $merchantRepository;
    }
    public function getAll(array $fields)
    {
        return $this->merchant_repo->getAll($fields);
    }
    public function getById($id, array $fields)
    {
        return $this->merchant_repo->getById($id, $fields ?? ['id, name']);
    }
    public function create(array $data)
    {
        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->merchant_repo->create($data);
    }
    public function update(int $id, array $data)
    {
        $fields = ['*'];
        $merchant = $this->merchant_repo->getById($id, $fields);

        if(isset($data['photo']) && $data['photo'] instanceof UploadedFile){
            if(!empty($merchant->photo)){
                $this->deletePhoto($merchant->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->merchant_repo->update($id, $data);
    }
    public function delete(int $id)
    {
        $fields = ['*'];
        $merchant = $this->merchant_repo->getById($id, $fields);
        if($merchant->photo){
            $this->deletePhoto($merchant->photo);
        }
        $this->merchant_repo->delete($id);
    }
    public function getByKeeperId(string $keeperId)
    {
        $fields = ['*'];
        return $this->merchant_repo->getByKeeperId($keeperId, $fields);
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('merchants', 'public');
    }
    private function deletePhoto(string $photoPath)
    {
        $relativePath = 'merchants/'.basename($photoPath);
        if(Storage::disk('public')->exists($relativePath)){
            Storage::disk('public')->delete($relativePath);
        }
    }
}
