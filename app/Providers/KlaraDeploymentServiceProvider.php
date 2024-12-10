<?php

namespace Modules\KlaraDeployment\app\Providers;

use Modules\KlaraDeployment\app\Console\Deployment;
use Modules\SystemBase\app\Providers\Base\ModuleBaseServiceProvider;

class KlaraDeploymentServiceProvider extends ModuleBaseServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'KlaraDeployment';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'klara-deployment';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $this->commands([
            Deployment::class
        ]);
    }


}
