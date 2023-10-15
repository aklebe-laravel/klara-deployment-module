<?php

namespace Modules\KlaraDeployment\Http\Livewire\DataTable;

use Modules\DataTable\Http\Livewire\DataTable\Base\BaseDataTable;

class DeploymentTask extends BaseDataTable
{
    /**
     * @return void
     */
    protected function initMount(): void
    {
        parent::initMount();

        // @todo: Not sure mount() is the right place for init this once
        $this->setSortAllCollections('rating', 'desc');
        $this->setSortAllCollections('updated_at', 'desc');
    }

    /**
     * @return array|array[]
     */
    public function getColumns(): array
    {
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
                'name'       => 'rating',
                'label'      => __('Rating'),
                'searchable' => true,
                'sortable'   => true,
                'format'     => 'number',
                'css_all'    => 'font-monospace text-end w-5',
            ],
            //            [
            //                'name'       => 'pivot.is_enabled',
            //                'label'      => __('Enabled'),
            //                'view'       => 'data-table::livewire.js-dt.tables.columns.bool-red-green',
            //                'css_all'    => 'text-center w-5',
            //                'sortable'   => true,
            //            ],
            //            [
            //                'name'       => 'code',
            //                'label'      => __('Code'),
            //                'searchable' => true,
            //                'sortable'   => true,
            //                'css_all'    => 'w-20',
            //            ],
            [
                'name'       => 'label',
                'label'      => __('Label'),
                'searchable' => true,
                'sortable'   => true,
                'view'       => 'data-table::livewire.js-dt.tables.columns.value-click-edit',
                'css_all'    => 'w-30',
            ],
            [
                'name'       => 'description',
                'label'      => __('Description'),
                'visible'    => true,
                'searchable' => true,
                'css_all'    => 'w-40',
            ],
            [
                'name'       => 'updated_at',
                'label'      => __('Updated'),
                'searchable' => true,
                'sortable'   => true,
                'view'       => 'data-table::livewire.js-dt.tables.columns.datetime-since',
            ],
        ];
    }

    //    /**
    //     * The base builder before all filter manipulations.
    //     * Usually used for all collections (default, selected, unselected), but can overwritten.
    //     *
    //     * @param  string  $collectionName
    //     *
    //     * @return Builder|null
    //     */
    //    public function getBaseBuilder(string $collectionName): ?Builder
    //    {
    //        /** @var Builder $builder */
    //        $builder = (SystemHelper::NamespaceEloquentModel.$this->getModelName())::query();//->withPivot(['is_enabled', 'position']);
    //        Log::debug($builder->toSql());
    //
    ////        if ($this->useUserFilter) {
    ////
    ////            $builder->whereUserId($this->getUserId());
    ////
    ////        }
    //
    //        return $builder;
    //    }

}
