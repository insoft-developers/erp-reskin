<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DynamicEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channels;
    public $event;
    public $refid;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $channel = '', string $event, $options = [
        'refid' => null,
        'data' => null,
    ])
    {
        $this->channels = ["pc-$channel", "pv-$channel"];
        $this->event = $event;

        if (isset($options['data'])) {
            $this->data = json_decode(json_encode($options['data']));
        }
        if (isset($options['refid'])) {
            $this->refid = $options['refid'];
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = $this->channels;
        $refid = $this->refid;
        $temps = [];
        foreach ($channels as $index => $channel) {
            $temps[] = !$index ? new Channel($channel) : new PrivateChannel($channel);

            if ($refid) {
                $temps[] = !$index ? new Channel("$channel.$refid") : new PrivateChannel("$channel.$refid");
                $this->channels[] = "$channel.$refid";
            }
        }

        return $temps;
    }

    public function broadcastAs()
    {
        // event name
        return $this->event;
    }
}
