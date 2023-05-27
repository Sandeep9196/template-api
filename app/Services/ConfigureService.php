<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\Configure;
use Illuminate\Http\JsonResponse;

class ConfigureService
{
    public function index($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Configure())->newQuery()->orderBy($sortBy, $sortOrder);
            $results = $query->select('configures.*')->paginate($perPage, ['*'], 'page', $page);
            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function botGlobalSave(array $data): JsonResponse
    {
        try {
            $bot = Bot::with('botGlobalConfiguration')->where('type', 'setting')->whereNull('customer_id')->first();

            if (!$bot) {
                $bot = Bot::create([
                    'type' => 'setting'
                ]);

                $configuration = new Configure([
                    'data' => json_encode($data),
                    'type' => 'bot'
                ]);
                $bot->botGlobalConfiguration()->save($configuration);
            } else {

                $bot['configuration'] = $bot['botGlobalConfiguration'];
                Configure::whereId($bot['configuration']['id'])->update(['data'=>json_encode($data)]);
                $bot = Bot::with('botGlobalConfiguration')->where('type', 'setting')->whereNull('customer_id')->first();

            }

            return response()->json([
                'data' => $bot,
                'messages' => ['Setting created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function botGlobal(array $data): JsonResponse
    {
        try {
            $bot = Bot::with('botGlobalConfiguration')->where('type', 'setting')->whereNull('customer_id')->first();

            return response()->json([
                'data' => $bot,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function mlmGlobalSave(array $data): JsonResponse
    {
        try {
            $mlm = Configure::where('type', 'mlm')->first();
            if (!$mlm) {
                $mlm = Configure::create([
                    'data' => json_encode($data),
                    'type' => 'mlm'
                ]);
            } else {
                $mlm = Configure::where('type', 'mlm')->update(['data'=>json_encode($data)]);
                $mlm = Configure::where('type', 'mlm')->first();
            }

            return response()->json([
                'data' => $mlm,
                'messages' => ['MLM Setting created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function mlmGlobal(array $data): JsonResponse
    {
        try {
            $mlm = Configure::where('type', 'mlm')->first();

            return response()->json([
                'data' => $mlm,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }


    public function orderGlobalSave(array $data): JsonResponse
    {
        try {
            $order = Configure::where('type', 'order')->first();
            if (!$order) {
                $order = Configure::create([
                    'data' => json_encode($data),
                    'type' => 'order'
                ]);
            } else {
                $order = Configure::where('type', 'order')->update(['data'=>json_encode($data)]);
                $order = Configure::where('type', 'order')->first();
            }

            return response()->json([
                'data' => $order,
                'messages' => ['Order Setting created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function orderGlobal(array $data): JsonResponse
    {
        try {
            $order = Configure::where('type', 'order')->first();

            return response()->json([
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }


    public function paymentGlobalSave(array $data): JsonResponse
    {
        try {
            $payment = Configure::where('type', 'payment')->first();
            if (!$payment) {
                $payment = Configure::create([
                    'data' => json_encode($data),
                    'type' => 'payment'
                ]);
            } else {
                $payment = Configure::where('type', 'payment')->update(['data'=>json_encode($data)]);
                $payment = Configure::where('type', 'payment')->first();
            }

            return response()->json([
                'data' => $payment,
                'messages' => ['Payment Setting created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function paymentGlobal(array $data): JsonResponse
    {
        try {
            $payment = Configure::where('type', 'payment')->first();

            return response()->json([
                'data' => $payment,
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }


}
