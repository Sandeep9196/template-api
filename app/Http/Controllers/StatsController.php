<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __construct(private StatsService $statsService)
    {
    }

    public function getOnlineMember(Request $request)
    {
        return  $this->statsService->getOnlineStats($request);
    }

    public function getThisMonthOnlineUsers(Request $request)
    {
        return  $this->statsService->getThisMonthOnlineUsers($request);
    }

    public function getRecentMemberOnline(Request $request)
    {
        return  $this->statsService->getRecentMemberOnline($request);
    }

    public function getRecentTransactions(Request $request)
    {
        return  $this->statsService->getRecentTransactions($request);
    }

    public function dashboard(Request $request)
    {
        
        return  $this->statsService->dashboard($request);
    }

    public function getThisMonthNewMembers(Request $request){
        return  $this->statsService->getThisMonthNewMembers($request);
    }

    public function slotPurchases(Request $request){
        return  $this->statsService->slotPurchases($request);
    }
    public function tranferAmount(Request $request){
        return  $this->statsService->tranferAmount($request);
    }

    public function getThisMontTransaction(Request $request){
        return  $this->statsService->getThisMontTransaction($request);
    }
}

