<?php

namespace Modules\KlaraDeployment\Services\TaskCommand;

use Modules\DeployEnv\Console\Base\DeployEnvBase;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;

class Process extends Base
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
     * @param $commandData
     * @return bool
     */
    public function runExec($commandData): bool
    {
//        Log::debug("Command data: ".print_r($commandData, true));
//        return false;

        if (!($line = data_get($commandData, 'line'))) {
            $this->error("Missing parameter line");
            return false;
        }

        if (($options = data_get($commandData, 'options', [])) && (is_array($options))) {
            $line = DeployEnvBase::addCommandOptions($line, $options);
        }

//        Log::debug($line, [__METHOD__]);
//        return false;

        //        $this->debug(__METHOD__);
        $this->dispatchBroadcast("Starting process: '$line'");


        //        $line = "ls -alph";
        $result = \Illuminate\Support\Facades\Process::forever()
            // force app root
            ->path(base_path())
            // start async call ...
            ->start($line, function (string $type, string $output) {

                $this->dispatchBroadcast($output);

            });

        return $result->wait()->successful();
    }

    /**
     * Simulating git update.
     *
     * @param $commandData
     * @return bool
     */
    public function simulateExec($commandData): bool
    {
        $this->dispatchBroadcast("Process simulation started ...");
        $this->debug(__METHOD__, $commandData);
        return true;
    }


}