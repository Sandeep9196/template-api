<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionFormRequest;
use App\Http\Requests\UpdateTransactionStatusFormRequest;
use App\Http\Requests\WithdrawFormRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->transactionService->paginate($request);
    }

    public function all()
    {
        return response()->json(Transaction::all(), 200);
    }

    public function store(TransactionFormRequest $request)
    {

        return $this->transactionService->store($request->all());
    }

    public function update(TransactionFormRequest $request, Transaction $transaction)
    {
        return $this->transactionService->update($transaction, $request->all());
    }

    public function delete(Transaction $transaction)
    {
        return $this->transactionService->delete($transaction);
    }

    public function updateStatus(UpdateTransactionStatusFormRequest $request)
    {
        return $this->transactionService->updateStatus($request->all());
    }

    public function tranferAmount(Request $request)
    {
        return $this->transactionService->tranferAmount($request->all());
    }
    public function withdraw(WithdrawFormRequest $request)
    {
        return $this->transactionService->withdraw($request);
    }

    public function depositResponse(Request $request)
    {
        return $this->transactionService->depositResponse($request);
    }

    
    

}
