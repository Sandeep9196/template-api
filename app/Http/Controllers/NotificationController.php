<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->notificationService->paginate($request);
    }
    public function update(Notification $notification,Request $request)
    {
        return $this->notificationService->update($notification,$request->all());
    }
}
