<?php
namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll(array $fields)
    {
        return Warehouse::select($fields)->with(['products.category'])->latest()->paginate(30);
    }
    public function getById(int $id, array $fields)
    {
        return Warehouse::select($fields)->with(['products.category'])->findOrFail($id);
    }
    public function create(array $data)
    {
        return Warehouse::create($data);
    }
    public function update(int $id, array $data)
    {
        $Warehouse = Warehouse::findOrFail($id);
        $Warehouse->update($data);
        return $Warehouse;
    }
    public function delete(int $id)
    {
        $Warehouse = Warehouse::findOrFail($id);
        $Warehouse->delete();
    }
}
