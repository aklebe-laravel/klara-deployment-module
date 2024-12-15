<?php

namespace Modules\KlaraDeployment\app\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 *
 */
class DeploymentDeploymentTask extends Pivot
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deployment_deployment_task';

    /**
     * var_list should always cast from json to an array and via versa
     *
     * @var string[]
     */
    protected $casts = [
        'var_list' => 'array'
    ];

}