<?php

namespace Modules\KlaraDeployment\app\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

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
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        if (app('website_base_config')->get('broadcast.enabled', false)) {

            //        $channelName = 'deployments.'.$this->processContainer['deployment_id'];
            $channelName = app('php_to_js')->get('default_console_channel');

            return [
                new PrivateChannel($channelName),
            ];

        }

        return [];
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
