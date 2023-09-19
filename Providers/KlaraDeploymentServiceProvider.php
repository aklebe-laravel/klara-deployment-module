<?php

namespace Modules\KlaraDeployment\Providers;

use App\Http\Kernel;
use Modules\KlaraDeployment\Console\Deployment;
use Modules\SystemBase\Providers\ModuleBaseServiceProvider;

class KlaraDeploymentServiceProvider extends ModuleBaseServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'KlaraDeployment';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'klaradeployment';

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
