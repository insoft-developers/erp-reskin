<?php

namespace App\Http\Controllers;

use App\Events\DynamicEvent;
use Illuminate\Http\Request;

class WebsocketController extends Controller
{
    public function test()
    {
        event(new DynamicEvent('order-channel', 'e1', [
            'refid' => 13411,
            'data' => [
                'message' => 'ok masuk'
            ]
        ]));
        return 'Event has been sent!';
    }

    public function index()
    {
        $data['view'] = 'websocket-index';
        return view('main.websocket.index', $data);
    }
}
