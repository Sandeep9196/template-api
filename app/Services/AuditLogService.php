<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use App\Models\LoginLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use OwenIt\Auditing\Models\Audit;

class AuditLogService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Audit())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });

            $query->when($request->user_id, function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });

            $query->when($request->event, function ($query) use ($request) {
                $query->where('event', $request->event);
            });

            $query->when($request->auditable_type, function ($query) use ($request) {
                $query->where('auditable_type', $request->auditable_type);
            });

            $results = $query->with(['user'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }
    public function paginateAdmins($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: '';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new LoginLog())->newQuery()->where('user_type', User::class)->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('login_logs.created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('login_logs.created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });
            $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'login_logs.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');

            $query->when($request->user_id, function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });

            $query->when($request->ip_address, function ($query) use ($request) {
                $query->where('ip_address', 'like', "$request->ip_address%");
            });

            $query->when($request->role_id, function ($query) use ($request) {
                if($request->role_id == 2 ){
                    $request->role_id = 1;
                }
                $query->where('model_has_roles.role_id', $request->role_id);
            });


            $query->select(['login_logs.*', 'roles.name as role_name','roles.id as role_id']);

            $results = $query->with(['user'])->groupBy('login_logs.created_at')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function paginateMembers($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new LoginLog())->newQuery()->where('user_type', Customer::class)->orderBy($sortBy, $sortOrder);

            $query->leftJoin('customers', 'customers.id', '=', 'login_logs.user_id');

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('login_logs.created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('login_logs.created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });

            $query->when($request->user_id, function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            });

            $query->when($request->ip_address, function ($query) use ($request) {
                $query->where('ip_address', 'like', "$request->ip_address%");
            });

            $query->when($request->member_id, function ($query) use ($request) {
                $query->where('customers.member_ID', $request->member_id);
            });
            $query->select(['login_logs.*']);

            $results = $query->with(['user'])->groupBy('login_logs.created_at')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getModels(): JsonResponse
    {
        try {
            $query = (new Audit())->newQuery();
            $results = $query->distinct('auditable_type')->select('auditable_type')->get();

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }
}
