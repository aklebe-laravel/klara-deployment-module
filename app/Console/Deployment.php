<?php

namespace Modules\KlaraDeployment\app\Console;

use Illuminate\Support\Facades\Log;
use Modules\KlaraDeployment\app\Models\Deployment as DeploymentModel;
use Modules\KlaraDeployment\app\Services\DeploymentService;
use Psr\Log\LogLevel;

class Deployment extends DeploymentBaseInherit
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployment:run {code?} {--simulate} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Deployment';

    /**
     * @var string|null
     */
    protected ?string $secretMaintenanceKey = null;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('debug')) {
            config(['app.debug' => true]);
        } else {
            // by default disable debug
            config(['app.debug' => false]);
        }

        Log::info(sprintf("========= %s =========", $this->signature));

        if (!$code = $this->getArgDeploymentCode()) {
            $this->message("Missing arg code");
            return self::FAILURE;
        }

        /** @var DeploymentModel $deployment */
        if (!($deployment = DeploymentModel::with([])->where('code', $code)->first())) {
            $this->message(sprintf("Code not found '%s'", $code));
            return self::FAILURE;
        }

        $this->message(sprintf("Deployment loaded '%s': '%s'", $deployment->code, $deployment->label), LogLevel::INFO);
        $this->message(sprintf("Enabled tasks %d/%d", $deployment->enabledTasks->count(), $deployment->tasks->count()), LogLevel::INFO);

        $deploymentService = app(DeploymentService::class);
        if ($this->option('simulate')) {
            $this->message(sprintf("SIMULATING deployment: %s", $deployment->code), LogLevel::INFO);
            $deploymentService->simulate($deployment);
        } else {
            $this->message(sprintf("STARTING deployment: %s", $deployment->code), LogLevel::INFO);
            $deploymentService->run($deployment);
        }

        return self::SUCCESS;
    }

}
