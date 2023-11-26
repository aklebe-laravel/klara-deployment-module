<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modules\KlaraDeployment\Models\DeploymentDeploymentTask
 *
 * @property int $deployment_id
 * @property int $deployment_task_id
 * @property int $is_enabled disable to avoid this task
 * @property int $position Position in list. Lower values first.
 * @property array|null $var_list json of vars merged with task vars
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentDeploymentTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentDeploymentTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentDeploymentTask query()
 * @mixin \Eloquent
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