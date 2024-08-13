<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseNotificationController extends Controller
{
    use ApiResponse;

    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')));
        $this->messaging = $factory->createMessaging();
    }

    public function sendToTopic(Request $request){
        try {
            $topic = $request->topic;

            $notification = [
                'title'=> $request->title,
                'body'=> $request->body,
            ];

            $data = $request->data;

            $message = CloudMessage::withTarget('topic',$topic)
                                    ->withNotification($notification)
                                    ->withData($data);

            $this->messaging->send($message);
            return $this->successResponse([
                'Topic' => $topic,
                'Notification' => $notification,
                'Data' => $data
            ],__('messages.firebase_notification.success.topic'));

        } catch (MessagingException $e) {
            return $this->errorResponse(__('messages.firebase_notification.fail.topic'),500,$e->getMessage());
        }

    }

    public function sentToSpecificDevice(Request $request){
        try {
            $devicetoken = $request->devicetoken;

            $notification = [
                'title'=> $request->title,
                'body'=> $request->body,
            ];

            // $data = $request->data;

            $message = CloudMessage::withTarget('token',$devicetoken)
                                    ->withNotification($notification);
                                    // ->withData($data);

            $this->messaging->send($message);
            return $this->successResponse([
                'token' => $devicetoken,
                'Notification' => $notification,
                // 'Data' => $data
            ],__('messages.firebase_notification.success.singleDevice'));

        } catch (MessagingException $e) {
            return $this->errorResponse(__('messages.firebase_notification.fail.singleDevice'),500,$e->getMessage());
        }

    }
}
