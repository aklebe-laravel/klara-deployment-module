<?php

namespace Modules\KlaraDeployment\Http\Livewire\DataTable;

use Modules\DataTable\Http\Livewire\DataTable\Base\BaseDataTable;
use Modules\KlaraDeployment\Models\Deployment as DeploymentModel;
use Modules\KlaraDeployment\Services\DeploymentService;

class Deployment extends BaseDataTable
{
    //    /**
    //     * @param $id
    //     */
    //    public function __construct($id = null)
    //    {
    //        parent::__construct($id);
    //
    //        $this->listeners += [
    //            'launchItem' => 'launchItem',
    //        ];
    //    }

    /**
     * @return void
     */
    protected function initMount(): void
    {
        parent::initMount();

        // @todo: Not sure mount() is the right place for init this once
        $this->setSortAllCollections('rating', 'desc', true);
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
            //                'name'       => 'code',
            //                'label'      => __('Code'),
            //                'searchable' => true,
            //                'sortable'   => true,
            //                'css_all'    => 'w-10',
            //            ],
            [
                'name'       => 'label',
                'label'      => __('Label'),
                'searchable' => true,
                'sortable'   => true,
                'options'    => [
                    'has_open_link' => $this->canEdit(),
                    'str_limit'     => 30,
                ],
                'css_all'    => 'w-30',
            ],
            //            [
            //                'name'       => 'updated_at',
            //                'label'      => __('Updated At'),
            //                'searchable' => true,
            //                'sortable'   => true,
            //                'view'       => 'data-table::livewire.js-dt.tables.columns.datetime-since',
            //            ],
            [
                'name'       => 'description',
                'label'      => __('Description'),
                'visible'    => true,
                'searchable' => true,
                'view'       => 'klara-deployment::livewire.js-dt.tables.columns.deployment-details',
                'css_all'    => 'w-40',
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
    ////        if ($this->useCollectionUserFilter) {
    ////
    ////            $builder->whereUserId($this->getUserId());
    ////
    ////        }
    //
    //        return $builder;
    //    }

    /**
     * @return array
     */
    public function getActionsColumn(): array
    {
        return [
            'label'   => 'Actions',
            //            'visible' => fn() => $this->editable,
            'visible' => true,
            'view'    => 'data-table::livewire.js-dt.tables.columns.run',
            'css_all' => 'text-end w-15',
        ];
    }

    /**
     * Register livewire function in $listeners[] !
     *
     * @param  mixed  $livewireId
     * @param  mixed  $itemId
     * @param  bool  $simulate
     * @return bool
     * @throws \Exception
     */
    public function launchItem(mixed $livewireId, mixed $itemId, bool $simulate = false): bool
    {
        if (!parent::launchItem($livewireId, $itemId)) {
            return false;
        }

        /** @var DeploymentModel $deployment */
        if (!($deployment = DeploymentModel::with([])->where('id', $itemId)->first())) {
            $this->addErrorMessage(sprintf("ID not found '%s'", $itemId));
            return false;
        }

        $deploymentService = new DeploymentService();
        //        $result = $deploymentService->run($deployment);
        //        return $result;

        if ($simulate) {
            $result = $deploymentService->simulate($deployment);
        } else {
            $result = $deploymentService->run($deployment);
        }

        if ($result) {
            $this->addSuccessMessage(sprintf("Successful %s ID %s!", $simulate ? 'simulated' : 'launched', $itemId));
            return true;
        };

        $this->addErrorMessage(sprintf("%s failed for ID %s!", $simulate ? 'Simulation' : 'Launch', $itemId));
        return false;
    }

}
