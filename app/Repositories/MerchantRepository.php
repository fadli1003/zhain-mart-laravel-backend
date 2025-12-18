<?php

namespace App\Repositories;

use App\Models\Merchant;

class MerchantRepository
{
    public function getAll(array $fields)
    {
        return Merchant::select($fields)->with(['keeper', 'products'])->latest()->paginate(10);
    }
    public function getById($id, $fields)
    {
        return Merchant::select($fields)->with(['keeper', 'products.category'])->findOrFail($id);
    }
    public function create($data)
    {
        return Merchant::create($data);
    }
    public function update($id, $data)
    {
        $merchant = Merchant::findOrFail($id);
        $merchant->update($data);
        return $merchant;
    }
    public function delete($id)
    {
        $merchant = Merchant::findOrFail($id);
        $merchant->delete();
    }
    public function getByKeeperId($keeperId, array $fields)
    {
        return Merchant::select($fields)->where('keeper_id', $keeperId)
                        ->with(['products.category'])->firstOrFail();
    }
}