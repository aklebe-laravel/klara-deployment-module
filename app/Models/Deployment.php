<?php

namespace Modules\KlaraDeployment\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Modules\WebsiteBase\app\Models\Base\TraitBaseModel;

/**
 * @mixin IdeHelperDeployment
 */
class Deployment extends Model
{
    use TraitBaseModel;

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

    ///**
    // * You can use this instead of newFactory()
    // *
    // * @var string
    // */
    //public static string $factory = DeploymentFactory::class;

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
        $tableName = app('system_base')->getModelTable(DeploymentTask::class);

        return $this->tasks()->where($tableName.'.is_enabled', true)->wherePivot('is_enabled', true);
    }

    /**
     * @return BelongsToMany
     */
    public function deploymentResults(): BelongsToMany
    {
        return $this->belongsToMany(DeploymentResult::class)->withTimestamps();
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
        return ['tasks'];
    }

}