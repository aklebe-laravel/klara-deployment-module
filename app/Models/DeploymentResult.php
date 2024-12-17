<?php

namespace Modules\KlaraDeployment\app\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperDeploymentResult
 */
class DeploymentResult extends Model
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
    protected $table = 'deployment_results';

    /**
     * results should always cast from json to an array and via versa
     *
     * @var string[]
     */
    protected $casts = [
        'results' => 'array',
    ];

}