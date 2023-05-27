<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberFormRequest;
use App\Services\MemberService;
use App\Models\Customer;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(private MemberService $memberService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->memberService->paginate($request);
    }

    public function all()
    {
        return $this->memberService->all();
    }

    public function store(MemberFormRequest $request)
    {
        return $this->memberService->store($request->all());
    }

    public function update(MemberFormRequest $request, Customer $member)
    {
        return $this->memberService->update($member, $request->all());
    }

    public function delete(Customer $member)
    {
        return $this->memberService->delete($member);
    }

    public function getCalculations(Request $request,Customer $member)
    {
        return $this->memberService->getCalculations($request->all(),$member);
    }
}
