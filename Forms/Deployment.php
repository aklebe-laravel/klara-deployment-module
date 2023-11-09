<?php

namespace Modules\KlaraDeployment\Forms;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Form\Forms\Base\ModelBase;

class Deployment extends ModelBase
{
    /**
     * Relations die standardmäßig in der Collection mit aufgebaut werden (deklariert wie in with(...)).
     * Dient auch als:
     * - Blacklist von Properties, die von array_diff_key() entfernt werden, um das Objekt separat zu speichern
     * - onAfterUpdateItem() um die Relations zu synchronisieren
     *
     * @var array[]
     */
    protected array $objectRelations = [
        'tasks',
    ];

    /**
     * Einzahl
     * @var string
     */
    protected string $objectFrontendLabel = 'Deployment';

    /**
     * Mehrzahl
     * @var string
     */
    protected string $objectsFrontendLabel = 'Deployments';

    /**
     *
     * @return array
     */
    public function getFormElements(): array
    {
        $parentFormData = parent::getFormElements();

        $defaultSettings = $this->getDefaultFormSettingsByPermission();

        return [
            ... $parentFormData,
            'title'        => $this->makeFormTitle($this->jsonResource, 'name'),
            'tab_controls' => [
                'base_item' => [
                    'tab_pages' => [
                        [
                            'tab'     => [
                                'label' => __('Common'),
                            ],
                            'content' => [
                                'form_elements' => [
                                    'id'          => [
                                        'html_element' => 'hidden',
                                        'label'        => __('ID'),
                                        'validator'    => [
                                            'nullable',
                                            'integer'
                                        ],
                                    ],
                                    'is_enabled'  => [
                                        'html_element' => 'select_yes_no',
                                        'label'        => __('Enabled'),
                                        'description'  => __('Enable/Disable this Deployment'),
                                        'validator'    => 'bool',
                                        'css_group'    => 'col-3',
                                    ],
                                    'rating'      => [
                                        'html_element' => 'number_int',
                                        'label'        => __('Rating'),
                                        'description'  => __('Your rated value (default 5000)'),
                                        'validator'    => [
                                            'integer',
                                            'Min:200',
                                            'Max:99999'
                                        ],
                                        'css_group'    => 'col-3',
                                    ],
                                    'code'        => [
                                        'html_element' => 'text',
                                        'label'        => __('Code'),
                                        'description'  => __('Unique deployment code'),
                                        'validator'    => [
                                            'required',
                                            'string',
                                            'Max:255'
                                        ],
                                        'css_group'    => 'col-6',
                                    ],
                                    'label'       => [
                                        'html_element' => 'text',
                                        'label'        => __('Label'),
                                        'description'  => __('Deployment title'),
                                        'validator'    => [
                                            'required',
                                            'string',
                                            'Max:255'
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                    'description' => [
                                        'html_element' => 'textarea',
                                        'label'        => __('Description'),
                                        'description'  => __('Detailed description'),
                                        'validator'    => [
                                            'nullable',
                                            'string',
                                            'Max:30000'
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                    'var_list'    => [
                                        'html_element' => 'object_to_json',
                                        //                                        'value' => function() { return 'xxx'.json_encode($this->jsonResource->var_list, JSON_PRETTY_PRINT);},
                                        'label'        => __('Var List'),
                                        'description'  => __('Variables as formatted json. See README.md for details'),
                                        'validator'    => [
                                            'nullable',
                                            'string',
                                            'Max:50000'
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                ],
                            ],
                        ],
                        [
                            // don't show if creating a new object ...
                            'disabled' => !$this->jsonResource->getKey(),
                            'tab'      => [
                                'label' => __('Tasks'),
                            ],
                            'content'  => [
                                'form_elements' => [
                                    'tasks' => [
                                        'html_element' => $defaultSettings['element_dt'],
                                        'label'        => __('Tasks'),
                                        'description'  => __('Deployment tasks assigned to this deployment'),
                                        'css_group'    => 'col-12',
                                        'options'      => [
                                            'form'          => 'klara-deployment::form.deployment-task',
                                            'table'         => 'klara-deployment::data-table.deployment-task',
                                            'table_options' => [
                                                'hasCommands' => $defaultSettings['can_manage'],
                                                'editable'    => $defaultSettings['can_manage'],
                                                'canAddRow'   => $defaultSettings['can_manage'],
                                                'removable'   => $defaultSettings['can_manage'],
                                            ],
                                        ],
                                        'validator'    => [
                                            'nullable',
                                            'array'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}