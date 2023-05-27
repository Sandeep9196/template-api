<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccountFormRequest;
use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function __construct(private BankAccountService $bankAccountService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->bankAccountService->paginate($request);
    }

    public function all()
    {
        return response()->json(BankAccount::all(), 200);
    }

    public function store(BankAccountFormRequest $request)
    {

        return $this->bankAccountService->store($request->all());
    }

    public function update(BankAccountFormRequest $request,BankAccount $bankAccount)
    {
        
        return $this->bankAccountService->update($bankAccount, $request->all());
    }

    public function delete(BankAccount $bankAccount)
    {
        return $this->bankAccountService->delete($bankAccount);
    }
}
