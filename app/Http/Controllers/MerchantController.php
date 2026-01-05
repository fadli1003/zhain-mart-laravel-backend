<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantRequest;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    private MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function index()
    {
        $fields = ['*'];
        $merchant = $this->merchantService->getAll($fields ? : ['*']);
        // $merchantResource = MerchantResource::collection($merchant);
        return response()->json(MerchantResource::collection($merchant));
    }
    // public function index()
    // {
    //     $fields = ['*'];
    //     $merchant = $this->merchantService->getAll($fields);
    //     return MerchantResource::collection($merchant);
    // }

    public function show(int $id)
    {
        try{
            $fields = ['id', 'name', 'photo', 'keeper_id'];
            $merchant = $this->merchantService->getById($id, $fields);

            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e){
            return response()->json([
                'message' => 'Merchant not found',
            ], 404);
        }
    }

    public function store(MerchantRequest $request)
    {
        $merchant = $this->merchantService->create($request->validated());
        return response()->json(new MerchantResource($merchant), 201);
    }

    public function update(MerchantRequest $request, int $id)
    {
        try{
            $merchant = $this->merchantService->update($id, $request->validated());
            return response()->json(new MerchantResource($merchant));
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Merchant not found',
            ]);
        }
    }

    public function destroy(int $id)
    {
        try{
            $this->merchantService->delete($id);
            return response()->json([
                'message' => 'Merchant deleted succesfully'
            ]);
        } catch(ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Merchant is not found',
            ]);
        }
    }

    public function getMyMerchantProfile()
    {
        $userId = Auth::id();
        try{
            $merchant = $this->merchantService->getByKeeperId($userId);
            return response()->json(new MerchantResource($merchant));
        } catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Merchant is not found for this user.',
            ]);
        }
    }
}
