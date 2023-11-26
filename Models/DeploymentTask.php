<?php

namespace Modules\KlaraDeployment\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Acl\Models\Base\TraitBaseModel;
use Modules\SystemBase\Models\Base\TraitModelAddMeta;

/**
 * Modules\KlaraDeployment\Models\DeploymentTask
 *
 * @property int $id
 * @property int $is_enabled disable this task for all deployments
 * @property int $rating
 * @property string|null $code unique dotted namespace
 * @property string|null $label label/short description
 * @property string|null $description description what this task will do
 * @property array|null $command_list json of commands
 * @property array|null $var_list json of vars merged with deployment vars
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Modules\KlaraDeployment\Models\Deployment> $deployments
 * @property-read int|null $deployments_count
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentTask loadByFrontend(?mixed $fieldValue, string $fieldNonNumeric)
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeploymentTask query()
 * @mixin \Eloquent
 */
class DeploymentTask extends Model
{
    use TraitBaseModel;
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
        });
    }

    /**
     * After replicated/duplicated/copied
     * but before save()
     *
     * @param  Model  $fromItem
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
        //        return ['deployments']; // DO NOT ADD THIS!
        return [];
    }


}