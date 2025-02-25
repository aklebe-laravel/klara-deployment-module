<?php

namespace Modules\KlaraDeployment\app\Http\Livewire\Form;

use Modules\Form\app\Http\Livewire\Form\Base\ModelBase;
use Modules\WebsiteBase\app\Services\WebsiteBaseFormService;

class DeploymentTask extends ModelBase
{
    /**
     * Relations die standardmäßig in der Collection mit aufgebaut werden (deklariert wie in with(...)).
     * Dient auch als:
     * - Blacklist von Properties, die von array_diff_key() entfernt werden, um das Objekt separat zu speichern
     * - onAfterUpdateItem() um die Relations zu synchronisieren
     *
     * @var array[]
     */
    public array $objectRelations = [
        'deployments',
    ];

    /**
     * Einzahl
     *
     * @var string
     */
    protected string $objectFrontendLabel = 'Deployment Task';

    /**
     * Mehrzahl
     *
     * @var string
     */
    protected string $objectsFrontendLabel = 'Deployment Tasks';

    /**
     * @return array
     */
    public function makeObjectInstanceDefaultValues(): array
    {
        return array_merge(parent::makeObjectInstanceDefaultValues(), [
            'is_enabled' => 0,
        ]);
    }

    /**
     *
     * @return array
     */
    public function getFormElements(): array
    {
        $parentFormData = parent::getFormElements();

        /** @var WebsiteBaseFormService $formService */
        $formService = app(WebsiteBaseFormService::class);

        return [
            ... $parentFormData,
            'title'        => $this->makeFormTitle($this->getDataSource(), 'name'),
            'tab_controls' => [
                'base_item' => [
                    'tab_pages' => [
                        [
                            'tab'     => [
                                'label' => __('Common'),
                            ],
                            'content' => [
                                'form_elements' => [
                                    'id'           => [
                                        'html_element' => 'hidden',
                                        'label'        => __('ID'),
                                        'validator'    => [
                                            'nullable',
                                            'integer',
                                        ],
                                    ],
                                    'is_enabled'   => [
                                        'html_element' => 'select',
                                        'options'      => $formService::getFormElementYesOrNoOptions(),
                                        'label'        => __('Enabled'),
                                        'description'  => __('Enable/Disable this Task'),
                                        'validator'    => 'bool',
                                        'css_group'    => 'col-3',
                                    ],
                                    'rating'       => [
                                        'html_element' => 'number_int',
                                        'label'        => __('Rating'),
                                        'description'  => __('Your rated value (default 5000)'),
                                        'validator'    => [
                                            'integer',
                                            'Min:200',
                                            'Max:99999',
                                        ],
                                        'css_group'    => 'col-3',
                                    ],
                                    'code'         => [
                                        'html_element' => 'text',
                                        'label'        => __('Code'),
                                        'description'  => __('Unique deployment task code'),
                                        'validator'    => [
                                            'required',
                                            'string',
                                            'Max:255',
                                        ],
                                        'css_group'    => 'col-6',
                                    ],
                                    'label'        => [
                                        'html_element' => 'text',
                                        'label'        => __('Label'),
                                        'description'  => __('Deployment task title'),
                                        'validator'    => [
                                            'required',
                                            'string',
                                            'Max:255',
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                    'description'  => [
                                        'html_element' => 'textarea',
                                        'label'        => __('Description'),
                                        'description'  => __('Detailed description'),
                                        'validator'    => [
                                            'nullable',
                                            'string',
                                            'Max:30000',
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                    'command_list' => [
                                        'html_element' => 'object_to_json',
                                        'label'        => __('Command List'),
                                        'description'  => __('Commands as formatted json. See README.md for details'),
                                        'validator'    => [
                                            'nullable',
                                            'string',
                                            'Max:50000',
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                    'var_list'     => [
                                        'html_element' => 'object_to_json',
                                        'label'        => __('Var List'),
                                        'description'  => __('Variables as formatted json. See README.md for details'),
                                        'validator'    => [
                                            'nullable',
                                            'string',
                                            'Max:50000',
                                        ],
                                        'css_group'    => 'col-12',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'visible' => $this->getDataSource()->deployment->id ?? false, // visible if parent deployment is the caller
                            'tab'     => [
                                'label' => __('Deployment Relation'),
                            ],
                            'content' => [
                                'form_elements' => [
                                    'deployment.pivot.is_enabled' => [
                                        'html_element' => 'select',
                                        'options'      => $formService::getFormElementYesOrNoOptions(),
                                        'label'        => __('Enabled'),
                                        'description'  => __('Enable/Disable this Task'),
                                        'validator'    => 'bool',
                                        'css_group'    => 'col-3',
                                    ],
                                    'deployment.pivot.position'   => [
                                        'html_element' => 'number_int',
                                        'label'        => __('Position'),
                                        'description'  => __('Position in parent deployment (default 1000)'),
                                        'validator'    => [
                                            'integer',
                                            'Min:200',
                                            'Max:99999',
                                        ],
                                        'css_group'    => 'col-3',
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
