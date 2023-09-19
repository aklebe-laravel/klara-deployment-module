<?php

namespace Modules\KlaraDeployment\Services;

use Illuminate\Support\Facades\Log;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;
use Modules\SystemBase\Services\Base\BaseService;

class DeploymentService extends BaseService
{
    /**
     * @param  Deployment  $deployment
     * @return bool
     */
    public function run(Deployment $deployment): bool
    {
        $this->debug(__METHOD__);

        /** @var DeploymentTask $task */
        foreach ($deployment->enabledTasks as $task) {
            $taskService = new DeploymentTaskService($task, $deployment);
            if (!$taskService->run($task)) {
                Log::error(sprintf("Task '%s' failed! Aborting deployment!", $task->code));
                return false;
            }
        }

        return true;
    }

    /**
     * @param  Deployment  $deployment
     * @return bool
     */
    public function simulate(Deployment $deployment): bool
    {
        $this->debug(__METHOD__);

        /** @var DeploymentTask $task */
        foreach ($deployment->enabledTasks as $task) {
            $taskService = new DeploymentTaskService($task, $deployment);
            if (!$taskService->simulate($task)) {
                Log::error(sprintf("Task '%s' failed! Aborting deployment!", $task->code));
                return false;
            }
        }

        return true;
    }

}