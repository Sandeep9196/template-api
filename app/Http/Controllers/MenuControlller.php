<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuFormRequest;
use App\Models\Group;
use App\Models\Menu;
use App\Models\Type;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuControlller extends Controller
{
    public function __construct(private MenuService $menuService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->menuService->paginate($request);
    }
    public function paginateApi(Request $request)
    {
        return $this->menuService->paginateApi($request);
    }

    public function all()
    {
        return response()->json(Menu::all(), 200);
    }
    public function allApi()
    {
        return response()->json(Menu::where('status','active'), 200);
    }
    public function getType()
    {
        return response()->json(Type::with('translations')->get(), 200);
    }

    public function getGroup()
    {
        return response()->json(Group::all(), 200);
    }

    public function storeGroup(Request $request)
    {
        return $this->menuService->storeGroup($request->all());
    }

    public function storeType(Request $request)
    {
        return $this->menuService->storeType($request->all());
    }

    public function store(MenuFormRequest $request)
    {
        return $this->menuService->store($request->all());
    }

    public function update(MenuFormRequest $request, Menu $menu)
    {
        return $this->menuService->update($menu, $request->all());
    }

    public function delete(Menu $menu)
    {
        return $this->menuService->delete($menu);
    }

    public function get(Request $request)
    {
        return $this->menuService->get($request);
    }
}
