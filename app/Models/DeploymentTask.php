<?php

namespace Modules\KlaraDeployment\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Modules\SystemBase\app\Models\Base\TraitModelAddMeta;

/**
 *
 */
class DeploymentTask extends Model
{
    use TraitModelAddMeta;

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

    ///**
    // * You can use this instead of newFactory()
    // *
    // * @var string
    // */
    //public static string $factory = DeploymentTaskFactory::class;

    /**
     * Ordered by position ASC.
     * If positions equal, ordered by created_at ASC.
     *
     * @return BelongsToMany
     */
    public function deployments(): BelongsToMany
    {
        //Log::info("XXX", [__METHOD__]);
        return $this->belongsToMany(Deployment::class)
                    ->using(DeploymentDeploymentTask::class)
                    ->withTimestamps()
                    ->orderByPivot('position')
                    ->orderByPivot('created_at')
            //            ->wherePivot('deployment_id', $this->id)
                    ->withPivot(['is_enabled', 'position', 'var_list']);
    }

    ///**
    // * Ordered by position ASC.
    // * If positions equal, ordered by created_at ASC.
    // *
    // * @return BelongsToMany
    // */
    //public function deployment()
    //{
    //    if ($this->relatedPivotModelId) {
    //        return $this->deployments()->where('deployment_id', $this->relatedPivotModelId);
    //    }
    //
    //    return $this->deployments()->whereId(false);
    //}

    /**
     * Returns null if $this->relatedPivotModelId missing.
     *
     * @return Attribute
     */
    public function deployment(): Attribute
    {
        return Attribute::make(get: function ($v) {
            if ($this->relatedPivotModelId) {
                return $this->deployments()
                    //->withPivot(['is_enabled', 'position', 'var_list'])
                            ->where('deployment_id', $this->relatedPivotModelId)
                            ->first();
            }

            return null;
            //        }, set: function ($v) {
            //            return $v;
        });
    }

    /**
     * After replicated/duplicated/copied
     * but before save()
     *
     * @param  Model  $fromItem
     *
     * @return void
     */
    public function afterReplicated(Model $fromItem): void
    {
        $this->code = $this->code.'-'.Str::orderedUuid()->toString();
    }

    /**
     * Returns relations to replicate.
     *
     * @return array
     */
    public function getReplicateRelations(): array
    {
        // return ['deployments']; // DO NOT ADD THIS!
        return [];
    }


}