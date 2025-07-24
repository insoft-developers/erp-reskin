<?php
  namespace App\Traits;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

trait FirebaseNotificationTrait {

    protected $messaging;
    public function __construct()
    {
        $serviceAccountPath = storage_path('randu-app-firebase-adminsdk-oe1yd-dc6dbe16a1.json');

        if (!file_exists($serviceAccountPath)) {
            throw new \Exception('Service account file not found at: ' . $serviceAccountPath);
        }

        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $this->messaging = $factory->createMessaging();
    }
    public function sendNotification($token, $title, $body, $data = [], $image = null)
    {
        if ($token == 'all') {
            $message = CloudMessage::withTarget('topic', 'all')
                                    ->withNotification([
                                        'title' => $title,
                                        'body' => $body,
                                        'image' => $image
                                    ])
                                    ->withData($data);
    
            return $this->messaging->send($message);
        } else {
            $message = CloudMessage::withTarget('token', $token)
                                ->withNotification([
                                    'title' => $title,
                                    'body' => $body,
                                    'image' => $image
                                ])
                                ->withData($data);

            return $this->messaging->send($message);
        }
    }
}