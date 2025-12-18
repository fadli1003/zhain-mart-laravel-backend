<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantProductRequest;
use App\Http\Requests\MerchantProductUpdateRequest;
use App\Services\MerchantProductService;
use Illuminate\Http\Request;

class MerchantProductController extends Controller
{
    private $merchantProductService;
    public function __construct(MerchantProductService $merchantProductService)
    {
        $this->merchantProductService = $merchantProductService;
    }

    public function store(MerchantProductRequest $request, int $merchantId)
    {
        $validated = $request->validated();
        $validated['merchant_id'] = $merchantId;
        $merchantProduct = $this->merchantProductService->assignProductToMerchant($validated);

        return response()->json([
           'message' => 'Product assigned in merchant succesfully.',
           'data' => $merchantProduct 
        ], 201);
    }
    public function update(MerchantProductUpdateRequest $request, int $merchantId, int $productId)
    {
        $validated = $request->validated();
        $merchantProduct = $this->merchantProductService->updateStock(
            $merchantId,
            $productId,
            $validated['stock'],
            $validated['warehouse_id']
        );

        return response()->json([
            'message' => 'Stock updated succesfully.',
            'data' => $merchantProduct,
        ]);
    }
    public function destroy(int $merchantId, int $productId)
    {
        $this->merchantProductService->removeProductFromMerchant($merchantId, $productId);
        return response()->json([
            'message' => ['Product detached from merchant succesfully.']
        ]);
    }
}
