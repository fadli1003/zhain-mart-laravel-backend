<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseProductUpdateRequest;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    private $warehouseService;
    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }
    public function attach(Request $request, $warehouseId)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:1',
        ]);

        $this->warehouseService->attachProduct(
            $warehouseId,
            $request->input('product_id'),
            $request->input('stock')
        );

        return response()->json(['message' => 'Product attached succesfully.']);
    }
    public function detach($warehouseId, $productId)
    {
        $this->warehouseService->detachProduct($warehouseId, $productId);
        return response()->json(['message' => 'Product detached succesfully.']);
    }
    public function update(WarehouseProductUpdateRequest $request, $warehouseId, $productId)
    {
        $warehouseProduct = $this->warehouseService->updateProductStock(
            $warehouseId,
            $productId,
            $request->validated()['stock']
        );
        return response()->json([
            'message' => 'Stock updated succesfully.',
            'data' => $warehouseProduct
        ]);
    }
}
