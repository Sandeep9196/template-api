<?php

namespace App\Http\Controllers;

use App\Services\NotFoundService;
use Illuminate\Http\Request;

class NotFoundController extends Controller
{
    public function __construct(private NotFoundService $notFouondService)
    {
    }

    public function index(Request $request)
    {
        return $this->notFouondService->index($request->all());
    }
}
