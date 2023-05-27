<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryFormRequest;
use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {
    }

    public function store(InventoryFormRequest $request)
    {
        return $this->inventoryService->store($request->all());
    }

    public function update(InventoryFormRequest $request,Inventory $inventory)
    {
        return $this->inventoryService->update($inventory, $request->all());
    }

    public function delete(Inventory $inventory)
    {
        return $this->inventoryService->delete($inventory);
    }


    public function all()
    {
        return response()->json(Inventory::all(), 200);
    }

    public function paginate(Request $request)
    {
        return $this->inventoryService->paginate($request);
    }

    public function getByProductId(Request $request, $productId)
    {
        return $this->inventoryService->getByProductId($request, $productId);
    }

    public function getLowStock(Request $request)
    {
        return $this->inventoryService->getLowStock($request);
    }

}
