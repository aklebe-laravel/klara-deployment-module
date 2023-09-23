<?php

namespace Modules\KlaraDeployment\Services\TaskCommand;

use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;
use Modules\SystemBase\Services\GitService;

class Git extends Base
{
    /** @var GitService */
    protected GitService $gitService;

    /**
     * @param  DeploymentTask  $deploymentTask
     * @param  Deployment  $deployment
     */
    public function __construct(DeploymentTask $deploymentTask, Deployment $deployment)
    {
        parent::__construct($deploymentTask, $deployment);

        $this->gitService = app(GitService::class);
    }

    /**
     * @param  string  $gitSrc
     * @param  string  $gitDestFullPath
     * @return bool
     */
    public function openOrCreateRepository(string $gitSrc, string $gitDestFullPath): bool
    {
        $justCreated = false;
        if (!is_dir($gitDestFullPath)) {
            mkdir($gitDestFullPath, 0775, true);
            if (!is_dir($gitDestFullPath)) {
                $this->error(sprintf("Unable to create directory: %s", $gitDestFullPath));
                return false;
            }
            $gitDestFullPath = realpath($gitDestFullPath);

            if (!$this->gitService->createRepository($gitSrc, $gitDestFullPath)) {
                $this->error(sprintf("Unable to create git repository: %s", $gitDestFullPath));
                return false;
            }

            $this->info(sprintf("Successful cloned git repository: '%s' to '%s'", $gitSrc, $gitDestFullPath));
            $justCreated = true;
        } else {
            $gitDestFullPath = realpath($gitDestFullPath);

            if (!$this->gitService->openRepository($gitDestFullPath, true)) {
                $this->error(sprintf("Unable to open git repository: %s", $gitDestFullPath));
                return false;
            }
        }

        // pull if not just fresh cloned ...
        if (!$justCreated) {
            if (!$this->gitService->repositoryPull()) {
                $this->error(sprintf("Unable to pull git repository: %s", $gitDestFullPath));
                return false;
            }
            $this->info(sprintf("Successful pulled git repository: '%s' to '%s'", $gitSrc, $gitDestFullPath));
        }

        return true;
    }

    /**
     * Executing a git update.
     *
     * @param $commandData
     * @return bool
     */
    public function runUpdate($commandData): bool
    {
        $this->debug(__METHOD__);

        if (!($gitSrc = data_get($commandData, 'src'))) {
            $this->error("Missing parameter src");
            return false;
        }

        if (!($gitDest = data_get($commandData, 'dest'))) {
            $this->error("Missing parameter dest");
            return false;
        }

        $gitDestFullPath = public_path('/storage/deployments/'.$gitDest);
        if (!$this->openOrCreateRepository($gitSrc, $gitDestFullPath)) {
            return false;
        }

        // branch optional
        $gitBranch = data_get($commandData, 'branch', '');
        if ($gitBranch) {
            if (!$this->gitService->ensureBranch($gitBranch)) {
                $this->error(sprintf("Unable to switch git branch: %s", $gitBranch));
                return false;
            }

            $this->info(sprintf("Successful switched to git branch: '%s'", $gitBranch));
        }

        return true;
    }

    /**
     * Simulating git update.
     *
     * @param $commandData
     * @return bool
     */
    public function simulateUpdate($commandData): bool
    {
        $this->debug(__METHOD__);
        return true;
    }


}