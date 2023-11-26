<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modules\KlaraDeployment\Models\DeploymentResult
 *
 * @property int $id
 * @property int $deployment_id
 * @property array|null $results json of result
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentResult query()
 * @mixin \Eloquent
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