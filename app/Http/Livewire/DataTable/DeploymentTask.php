<?php

namespace Modules\KlaraDeployment\app\Http\Livewire\DataTable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Modules\DataTable\app\Http\Livewire\DataTable\Base\BaseDataTable;

class DeploymentTask extends BaseDataTable
{
    /**
     * @var array|string[]
     */
    protected array $objectRelations = ['deployments'];

    /**
     * Overwrite to init your sort orders before session exists
     *
     * @return void
     */
    protected function initSort(): void
    {
        // @todo: Not sure mount() is the right place for init this once
        if (data_get($this->parentData, 'id', false)) {
            $this->setSortAllCollections('deployment.pivot.position', 'asc');
            $this->setSortAllCollections('rating', 'desc');
        } else {
            $this->setSortAllCollections('rating', 'desc');
            $this->setSortAllCollections('deployments_count', 'desc');
            $this->setSortAllCollections('updated_at', 'desc');
        }
    }

    /**
     * @return array|array[]
     */
    public function getColumns(): array
    {
        $pivotExists = (bool) data_get($this->parentData, 'id', false);

        return [
            [
                'name'       => 'id',
                'label'      => __('ID'),
                'searchable' => true,
                'sortable'   => true,
                'format'     => 'number',
                'css_all'    => 'text-muted font-monospace text-end w-5',
            ],
            [
                'name'     => 'is_enabled',
                'label'    => __('Enabled'),
                'view'     => 'data-table::livewire.js-dt.tables.columns.bool-red-green',
                'css_all'  => 'text-center w-5',
                'sortable' => true,
            ],
            [
                'name'     => 'deployment.pivot.is_enabled',
                'label'    => __('Enabled'),
                'view'     => 'data-table::livewire.js-dt.tables.columns.bool-red-green',
                'css_all'  => 'text-center w-5',
                'sortable' => false,
                'visible'  => (bool) data_get($this->parentData, 'id', false),
            ],
            [
                'name'       => 'deployment.pivot.position',
                'label'      => __('Position'),
                'searchable' => $pivotExists,
                'sortable'   => false,
                'format'     => 'number',
                'css_all'    => 'font-monospace text-end w-5',
                'visible'    => $pivotExists,
            ],
            [
                'name'       => 'rating',
                'label'      => __('Rating'),
                'searchable' => true,
                'sortable'   => true,
                'format'     => 'number',
                'css_all'    => 'font-monospace text-end w-5',
            ],
            [
                'name'       => 'deployments_count',
                'label'      => __('Rels'),
                'searchable' => false,
                'sortable'   => $pivotExists,
                'format'     => 'number',
                'css_all'    => 'font-monospace text-end w-5',
            ],
            [
                'name'       => 'label',
                'label'      => __('Label'),
                'searchable' => true,
                'sortable'   => true,
                'options'    => [
                    'has_open_link' => $this->canEdit(),
                    'str_limit'     => 30,
                ],
                'css_all'    => 'w-25',
            ],
            [
                'name'       => 'description',
                'label'      => __('Description'),
                'visible'    => true,
                'searchable' => true,
                'css_all'    => 'w-35',
            ],
            [
                'name'       => 'updated_at',
                'label'      => __('Updated At'),
                'searchable' => true,
                'sortable'   => true,
                'view'       => 'data-table::livewire.js-dt.tables.columns.datetime-since',
            ],
        ];
    }

    /**
     * used for all not self::COLLECTION_NAME_SELECTED_ITEMS
     *
     * @param  string  $collectionName
     *
     * @return Builder|null
     * @throws Exception
     */
    public function getBaseBuilder(string $collectionName): ?Builder
    {
        // incl "deployments_count"
        return parent::getBaseBuilder($collectionName)->withCount('deployments');
    }

}
