<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class MemberService
{

    public function __construct(private CustomerService $customerService)
    {
    }

    public function store(array $data): JsonResponse
    {
        try {

            $data['referral_code'] = generateReferralCode(6);
            Customer::create($data);

            return response()->json([
                'messages' => ['Member created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($member, array $data): JsonResponse
    {

        try {
            $data['first_name'] = $data['first_name'] == "" ? $member['first_name'] : $data['first_name'];
            $data['last_name'] = $data['last_name'] == "" ? $member['last_name'] : $data['last_name'];
            $data['display_name'] = $data['display_name'] == "" ? $member['display_name'] : $data['display_name'];
            $data['phone_number'] = $data['phone_number'] == "" ? $member['phone_number'] : $data['phone_number'];
            $data['idd'] = $data['idd'] == "" ? $member['idd'] : $data['idd'];
            $data['email'] = $data['email'] == "" ? $member['email'] : $data['email'];
            $member->update($data);

            return response()->json([
                'messages' => ['Member updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($member): JsonResponse
    {
        try {
            $member->delete();

            return response()->json([
                'messages' => ['Member deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $customerIds = Bot::where(['type' => 'customer'])->get()->pluck('customer_id');
            $results = Customer::whereNotIn('id', $customerIds)->get();
            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Customer())->newQuery()->orderBy($sortBy, $sortOrder);
            $query->when($request->search, function ($query) use ($request) {
                $query->where('first_name', 'like', "%$request->search%")
                    ->orWhere('last_name', 'like', "%$request->search%")
                    ->orWhere('display_name', 'like', "%$request->search%")
                    ->orWhere('phone_number', 'like', "%$request->search%");
            });
            $query->when($request->member_ID, function ($query) use ($request) {
                $query->where('member_ID', 'like', "%$request->member_ID%");
            });

            $results = $query->select('customers.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function getCalculations($data, $customer): JsonResponse
    {
        return $this->customerService->getCalculations($data, $customer);
    }
}
