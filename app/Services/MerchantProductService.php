<?php
namespace App\Services;

use App\Models\Merchant;
use App\Repositories\MerchantProductRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\WarehouseProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MerchantProductService
{
    private MerchantRepository $merchant_repo;
    private MerchantProductRepository $merchantProduct_repo;
    private WarehouseProductRepository $warehouseProduct_repo;

    public function __construct(
        MerchantRepository $merchant_repo,
        MerchantProductRepository $merchantProduct_repo,
        WarehouseProductRepository $warehouseProduct_repo
    )
    {
        $this->merchantProduct_repo = $merchantProduct_repo;
        $this->warehouseProduct_repo = $warehouseProduct_repo;
        $this->merchant_repo = $merchant_repo;
    }

    public function assignProductToMerchant(array $data)
    {
        return DB::transaction(function () use ($data){
            $warehouseProduct = $this->warehouseProduct_repo->getByWarehouseAndProduct(
                $data['warehouse_id'],
                $data['product_id']
            );

            if(!$warehouseProduct || $warehouseProduct->stock < $data['stock']){
                throw ValidationException::withMessages([
                    'stock' => ['Insufficient stock in warehouse.']
                ]);

            }

            $existingProduct = $this->merchantProduct_repo->getByMerchantAndProduct(
                $data['merchant_id'],
                $data['product_id']
            );
            if($existingProduct){
                throw ValidationException::withMessages([
                    'product' => ['Product already exists in this merchant.']
                ]);
            }

            $this->warehouseProduct_repo->updateStock(
                $data['warehouse_id'],
                $data['product_id'],
                $warehouseProduct->stock - $data['stock']
            );
        });
    }

    public function updateStock(int $merchantId, int $productId, int $warehouseId, int $newStock)
    {
        return DB::transaction(function () use ($merchantId, $productId, $newStock, $warehouseId){
            $exist = $this->merchantProduct_repo->getByMerchantAndProduct($merchantId, $productId);
            $warehouseProduct = $this->warehouseProduct_repo->getByWarehouseAndProduct($warehouseId, $productId);

            if(!$exist){
                throw ValidationException::withMessages([
                    'product' => ['Product not assigned to this merchant.']
                ]);
            }

            if(!$warehouseId){
                throw ValidationException::withMessages([
                    'warehouse_id' => ['Warehouse ID is required when increasing stock.']
                ]);
            }

            $currentStock = $exist->stock;
            if($newStock > $currentStock){
                $diff = $newStock - $currentStock;
                if(!$warehouseProduct || $warehouseProduct->stock < $diff){
                    throw ValidationException::withMessages([
                        'stock' => ['Insufficient stock in warehouse.']
                    ]);
                }

                $this->warehouseProduct_repo->updateStock(
                    $warehouseId,
                    $productId,
                    $warehouseProduct->stock - $diff
                );
            }elseif($newStock < $currentStock){
                $diff = $currentStock - $newStock;
                if(!$warehouseProduct){
                    throw ValidationException::withMessages([
                        'warehouse' => ['Product not found in this warehouse.']
                    ]);
                }

                $this->warehouseProduct_repo->updateStock(
                    $warehouseId,
                    $productId,
                    $warehouseProduct->stock +  $diff
                );
            }

            return $this->merchantProduct_repo->updateStock($merchantId, $productId, $newStock);
        });
    }

    public function removeProductFromMerchant(int $merchantId, int $productId)
    {
        // $merchant = Merchant::findOrFail($merchantId);
        $merchant = $this->merchant_repo->getById($merchantId, $fields ?? ['*']);
        if(!$merchant){
            throw ValidationException::withMessages([
                'product' => ['Merchant not found.']
            ]);
        }

        $exist = $this->merchantProduct_repo->getByMerchantAndProduct($merchantId, $productId);
        if(!$exist){
            throw ValidationException::withMessages([
                'product' => ['Product not assigned to this merchant.']
            ]);
        }
        $merchant->products()->detach($productId);
    }
}
