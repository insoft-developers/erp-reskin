<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Traits\FirebaseNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use FirebaseNotificationTrait;

    public function sendPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token_fcm' => 'required',
            'title' => 'required',
            'body' => 'required',
            'data' => 'nullable|array',
            'image' => 'nullable',
        ]);

        $token = $request->token_fcm;
        $title = $request->title;
        $body = $request->body;
        $data = $request->data;
        $image = $request->image;

        try {
            $this->sendNotification($token, $title, $body, $data, $image);
            Log::info("Success Send Notification\nToken : $token");

            return response()->json([
                'message' => 'Success Send Notification',
                'success' => true
            ]);
        } catch (\Throwable $th) {
            Log::info("Failed Send Notification\nToken : $token");

            return response()->json([
                'message' => 'Failed Send Notification',
                'success' => false
            ]);
        }
    }
}
