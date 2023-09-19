<?php

namespace Modules\KlaraDeployment\Services;

use Illuminate\Support\Str;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;
use Modules\KlaraDeployment\Services\TaskCommand\Base;
use Modules\SystemBase\Services\Base\BaseService;

class DeploymentTaskCommandService extends BaseService
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
     * @var DeploymentTask
     */
    protected DeploymentTask $task;

    /**
     * Prepare inherited class and command parts.
     *
     * @param  array  $commandData
     * @return array|bool
     */
    protected function getCommandClassData(array $commandData): array|bool
    {
        $commandString = data_get($commandData, 'cmd');
        $commandParts = explode(':', $commandString);

        // Get generic class like Git, Ftp or whatever ...
        $class = __NAMESPACE__.'\\TaskCommand\\'.ucfirst(Str::camel($commandParts[0]));
        if (!class_exists($class)) {
            $this->error(sprintf("Task command class not defined: %s >> %s", $commandParts[0], $class));
            return false;
        }

        return [
            'command_parts' => $commandParts,
            'class' => $class
        ];
    }

    /**
     * @param  array  $commandData
     * @return bool
     */
    public function run(array $commandData): bool
    {
        $this->debug(__METHOD__);

        if (($commandClassData = $this->getCommandClassData($commandData)) === false) {
            return false;
        }

        /** @var Base $taskCommandClassInstance */
        $taskCommandClassInstance = new $commandClassData['class']($this->deploymentTask, $this->deployment);

        try {
            $commandData = $taskCommandClassInstance->parseCommandData($commandData);
            return $taskCommandClassInstance->run($commandClassData['command_parts'], $commandData);
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            $this->error($ex->getTraceAsString());
            return false;
        }
    }

    /**
     * @param  array  $commandData
     * @return bool
     */
    public function simulate(array $commandData): bool
    {
        $this->debug(__METHOD__);

        if (($commandClassData = $this->getCommandClassData($commandData)) === false) {
            return false;
        }

        /** @var Base $taskCommandClassInstance */
        $taskCommandClassInstance = new $commandClassData['class']($this->deploymentTask, $this->deployment);

        try {
            $commandData = $taskCommandClassInstance->parseCommandData($commandData);
            return $taskCommandClassInstance->simulate($commandClassData['command_parts'], $commandData);
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            $this->error($ex->getTraceAsString());
            return false;
        }
    }

}