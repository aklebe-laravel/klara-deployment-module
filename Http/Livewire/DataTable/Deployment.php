<?php

namespace Modules\KlaraDeployment\Http\Livewire\DataTable;

use Modules\DataTable\Http\Livewire\DataTable\Base\BaseDataTable;
use Modules\KlaraDeployment\Models\Deployment as DeploymentModel;
use Modules\KlaraDeployment\Services\DeploymentService;

class Deployment extends BaseDataTable
{
    /**
     * Overwrite to init your sort orders before session exists
     * @return void
     */
    protected function initSort(): void
    {
        $this->setSortAllCollections('rating', 'desc');
    }

    /**
     * Runs on every request, after the component is mounted or hydrated, but before any update methods are called
     *
     * @return void
     */
    protected function initBooted(): void
    {
        parent::initBooted();

        $this->rowCommands = [
            'launch_item'   => 'data-table::livewire.js-dt.tables.columns.buttons.launch-item',
            'simulate_item' => 'data-table::livewire.js-dt.tables.columns.buttons.simulate-item',
            ...$this->rowCommands
        ];
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
