<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     *
     * @var array
     */
    protected $appends = ['deployment'];

    /**
     * var_list should always cast from json to an array and via versa
     *
     * @var string[]
     */
    protected $casts = [
        'command_list' => 'array',
        'var_list'     => 'array',
    ];

    /**
     * its like a parent id
     * @var int|null
     */
    public ?int $relatedPivotModelId = null ;

    /**
     * Ordered by position ASC.
     * If positions equal, ordered by created_at ASC.
     *
     * @return BelongsToMany
     */
    public function deployments(): BelongsToMany
    {
        return $this->belongsToMany(Deployment::class)
            ->using(DeploymentDeploymentTask::class)
            ->withTimestamps()
            ->orderByPivot('position')
            ->orderByPivot('created_at')
//            ->wherePivot('deployment_id', $this->id)
            ->withPivot(['is_enabled', 'position', 'var_list']);
    }

    /**
     * Returns null if $this->relatedPivotModelId missing.
     *
     * @return Attribute
     */
    public function deployment(): Attribute
    {
        return Attribute::make(get: function ($v) {
            if ($this->relatedPivotModelId) {
                return $this->deployments()->where('deployment_id', $this->relatedPivotModelId)->first();
            }

            return null;
//        }, set: function ($v) {
//            return $v;
        },);
    }


//    public function addParentDeploymentPivot(int $deploymentId)
//    {
//        $this->deployment = $this->deployments()->wherePivot('deployment_id', $deploymentId)->first();
//        return $this->deployment;
//    }

}