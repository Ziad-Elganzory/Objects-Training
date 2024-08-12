<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class PushNotificationController extends Controller
{
    public function sendPushNotification()
    {
        $firebase = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $messaging = $firebase->createMessaging();

        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'Hello from Firebase!',
                'body' => 'This is a test notification.'
            ],
            'topic' => 'global'
        ]);

        $messaging->send($message);

        return response()->json(['response' => 'Push notification sent successfully','Message'=>$message]);
    }
}
