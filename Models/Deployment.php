<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\SystemBase\Helpers\SystemHelper;

class Deployment extends Model
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
    protected $table = 'deployments';

    /**
     * var_list should always cast from json to an array and via versa
     *
     * @var string[]
     */
    protected $casts = [
        'var_list' => 'array',
    ];

    /**
     * Ordered by position ASC.
     * If positions equal, ordered by created_at ASC.
     *
     * @return BelongsToMany
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(DeploymentTask::class)
            ->using(DeploymentDeploymentTask::class)
            ->withTimestamps()
            ->orderByPivot('position')
            ->orderByPivot('created_at')
            ->withPivot(['is_enabled', 'position', 'var_list']);
    }

    /**
     * @return BelongsToMany
     */
    public function enabledTasks(): BelongsToMany
    {
        // find table name to avoid ambiguous columns
        $tableName = SystemHelper::getModelTable(DeploymentTask::class);
        return $this->tasks()->where($tableName.'.is_enabled', true)->wherePivot('is_enabled', true);
    }

    /**
     * @return BelongsToMany
     */
    public function deploymentResults(): BelongsToMany
    {
        return $this->belongsToMany(DeploymentResult::class)->withTimestamps();
    }


}