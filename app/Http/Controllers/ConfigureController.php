<?php

namespace App\Http\Controllers;

use App\Models\Configure;
use App\Services\ConfigureService;
use Illuminate\Http\Request;

class ConfigureController extends Controller
{
    public function __construct(private ConfigureService $configureService)
    {
    }

    public function index(Request $request)
    {
        return $this->configureService->index($request);
    }

    public function botGlobal(Request $request)
    {
        return $this->configureService->botGlobal($request->all());
    }

    public function botGlobalSave(Request $request)
    {
        return $this->configureService->botGlobalSave($request->all());
    }

    public function mlmGlobal(Request $request)
    {
        return $this->configureService->mlmGlobal($request->all());
    }

    public function mlmGlobalSave(Request $request)
    {
        return $this->configureService->mlmGlobalSave($request->all());
    }


    public function orderGlobal(Request $request)
    {
        return $this->configureService->orderGlobal($request->all());
    }

    public function orderGlobalSave(Request $request)
    {
        return $this->configureService->orderGlobalSave($request->all());
    }

    public function paymentGlobal(Request $request)
    {
        return $this->configureService->paymentGlobal($request->all());
    }

    public function paymentGlobalSave(Request $request)
    {
        return $this->configureService->paymentGlobalSave($request->all());
    }

}
