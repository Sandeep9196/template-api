<?php

namespace App\Http\Controllers;

use App\Services\SettlementService;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function __construct(private SettlementService $settlementService)
    {
    }
    public function paginate(Request $request)
    {
        return $this->settlementService->paginate($request);
    }
    public function getTotalSettlement(Request $request)
    {
        return $this->settlementService->getTotalSettlement($request);
    }
}
