<?php

namespace Modules\KlaraDeployment\app\Services\TaskCommand;

use Modules\KlaraDeployment\app\Models\Deployment;
use Modules\KlaraDeployment\app\Models\DeploymentTask;

class Ftp extends Base
{
    /**
     * @param  DeploymentTask  $deploymentTask
     * @param  Deployment  $deployment
     */
    public function __construct(DeploymentTask $deploymentTask, Deployment $deployment)
    {
        parent::__construct($deploymentTask, $deployment);
    }

    /**
     * Executing a git update.
     *
     * @param array $commandData
     * @return bool
     */
    public function runUpload(array $commandData): bool
    {
        $this->debug(__METHOD__, $commandData);

        return true;
    }

    /**
     * Simulating a git update.
     *
     * @param array $commandData
     * @return bool
     */
    public function simulateUpload(array $commandData): bool
    {
        $this->debug(__METHOD__, $commandData);

        return true;
    }
}