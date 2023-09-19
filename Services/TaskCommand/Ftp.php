<?php

namespace Modules\KlaraDeployment\Services\TaskCommand;

use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;

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