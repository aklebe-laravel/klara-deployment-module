<?php

namespace Modules\KlaraDeployment\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DeploymentConsole implements ShouldBroadcast
{
    use Dispatchable;

    //    use SerializesModels;

    /**
     * @var array
     */
    public array $processContainer = [];

    /**
     * Create a new event instance.
     * @param  array  $processContainer
     */
    public function __construct(array $processContainer)
    {
        $this->processContainer = $processContainer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        //        $channelName = 'deployments.'.$this->processContainer['deployment_id'];
        $channelName = app('php_to_js')->get('default_console_channel');

        //        Log::debug(__METHOD__);
        //        Log::debug($channelName);
        return [
            new PrivateChannel($channelName),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'deployment.running';
    }
}
