<?php

namespace Modules\KlaraDeployment\Providers;

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
}
