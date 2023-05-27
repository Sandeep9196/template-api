<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankAccountService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new BankAccount())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });            
            $query->when($request->search, function ($query) use ($request) {
                $query->where('bank_name', 'like', "%$request->search%")
                        ->orWhere('account_name', 'like', "%$request->search%")
                        ->orWhere('account_no', 'like', "%$request->search%")
                        ->orWhere('account_type', 'like', "%$request->search%")
                        ->orWhere('remark', 'like', "%$request->search%");
            });
            
            $results = $query->select('bank_accounts.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $data['member_id'] = Auth::id();
                $bankAccount = BankAccount::create($data);
            });

            return response()->json([
                'messages' => ['Bank Account created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($bankAccount, array $data): JsonResponse
    {
        try {
            $bankAccount->update($data);
            return response()->json([
                'messages' => ['Bank Account updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($bankAccount): JsonResponse
    {
        try {
            //check if bank account in use
            if(sizeof($bankAccount->transactions) > 0)
                return response()->json([
                    'messages' => ['Bank Account already used, can not delete!'],
                ], 400);

            $bankAccount->delete();

            return response()->json([
                'messages' => ['Bank Account deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
