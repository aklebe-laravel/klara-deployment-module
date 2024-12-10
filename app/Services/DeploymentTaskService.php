<?php

namespace Modules\KlaraDeployment\app\Services;

use Modules\KlaraDeployment\app\Models\Deployment;
use Modules\KlaraDeployment\app\Models\DeploymentTask;
use Modules\SystemBase\app\Services\Base\BaseService;

class DeploymentTaskService extends BaseService
{
    /**
     * @var DeploymentTask
     */
    protected DeploymentTask $deploymentTask;

    /**
     * @var Deployment
     */
    protected Deployment $deployment;

    /**
     * @var int
     */
    public int $successTaskCommands = 0;

    /**
     * @param  DeploymentTask  $deploymentTask
     * @param  Deployment  $deployment
     */
    public function __construct(DeploymentTask $deploymentTask, Deployment $deployment)
    {
        parent::__construct();
        $this->deploymentTask = $deploymentTask;
        $this->deployment = $deployment;
    }

    /**
     * Run task.
     *
     * @param  DeploymentTask  $deploymentTask
     * @return int The amount of successfully task commands
     */
    public function run(DeploymentTask $deploymentTask): int
    {
        $this->deploymentTask = $deploymentTask;

        if (!$deploymentTask->command_list) {
            return 0;
        }

//        $this->info(sprintf("Run Task: '%s' ('%s' - position: %d)", $deploymentTask->label, $deploymentTask->code,
//            $deploymentTask->pivot->position));

        return $this->processTaskCommandList($deploymentTask->command_list);
    }

    /**
     * Simulate task.
     *
     * @param  DeploymentTask  $deploymentTask
     * @return int The amount of successfully task commands
     */
    public function simulate(DeploymentTask $deploymentTask): int
    {
        $this->deploymentTask = $deploymentTask;

        if (!$deploymentTask->command_list) {
            return 0;
        }

//        $this->info(sprintf("Run Task: '%s' ('%s' - position: %d)", $deploymentTask->label, $deploymentTask->code,
//            $deploymentTask->pivot->position));

        return $this->simulateTaskCommandList($deploymentTask->command_list);
    }

    /**
     * @param  array  $taskCommandList
     * @return bool The amount of successfully task commands
     */
    private function processTaskCommandList(array $taskCommandList): bool
    {
        $deploymentTaskCommandService = new DeploymentTaskCommandService($this->deploymentTask, $this->deployment);

        $this->successTaskCommands = 0;

        $this->debug(sprintf("Processing task commands %d", count($taskCommandList)));
        foreach ($taskCommandList as $commandData) {
            if (!$deploymentTaskCommandService->run($commandData)) {
                return false;
            }
            $this->successTaskCommands++;
        }

        return true;
    }

    /**
     * @param  array  $taskCommandList
     * @return bool The amount of successfully task commands
     */
    private function simulateTaskCommandList(array $taskCommandList): bool
    {
        $deploymentTaskCommandService = new DeploymentTaskCommandService($this->deploymentTask, $this->deployment);

        $this->successTaskCommands = 0;

        $this->debug(sprintf("Simulate task commands %d", count($taskCommandList)));
        foreach ($taskCommandList as $commandData) {
            if (!$deploymentTaskCommandService->simulate($commandData)) {
                return false;
            }
            $this->successTaskCommands++;
        }

        return true;
    }

}