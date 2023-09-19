<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Model;

class DeploymentTask extends Model
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
    protected $table = 'deployment_tasks';

    /**
     * var_list should always cast from json to an array and via versa
     *
     * @var string[]
     */
    protected $casts = [
        'command_list' => 'array',
        'var_list'     => 'array',
    ];

}