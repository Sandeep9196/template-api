<?php

namespace App\Http\Controllers;

use App\Http\Requests\WinningRequest;
use Illuminate\Http\Request;
use App\Services\WinningService;

class WinningController extends Controller
{
    public function __construct(private WinningService $winningService)
    {
    }

    public function createWinner(WinningRequest $winning)
    {
        return $this->winningService->createWinner($winning->all());
    }

    public function getPredicted(WinningRequest $winning)
    {
        return $this->winningService->getPredicted($winning->all());
    }

    public function paginate(Request $request)
    {
        return $this->winningService->paginate($request);
    }
    public function generateDeal(WinningRequest $request)
    {
        return $this->winningService->generateDeal($request->all());
    }
    public function winnerList(Request $request)
    {
        return $this->winningService->winnerList($request);
    }
}
