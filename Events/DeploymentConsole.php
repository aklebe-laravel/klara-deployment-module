<?php

namespace Modules\KlaraDeployment\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;

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
     * @param  Deployment  $deployment
     * @param  DeploymentTask  $deploymentTask
     * @param  string  $textMessage
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
        $channelName = 'deployments.default-console';

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
