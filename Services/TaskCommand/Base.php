<?php

namespace Modules\KlaraDeployment\Services\TaskCommand;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\KlaraDeployment\Events\DeploymentConsole;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;
use Modules\KlaraDeployment\Services\DeploymentTaskParserService;
use Modules\SystemBase\Helpers\SystemHelper;
use Modules\SystemBase\Services\Base\BaseService;

class Base extends BaseService
{
    /**
     * Overwrite or declare this in constructor to define relevant parameters.
     *
     * @var array
     */
    protected array $validateParameters = [];

    /**
     * @var DeploymentTask
     */
    protected DeploymentTask $deploymentTask;

    /**
     * @var Deployment
     */
    protected Deployment $deployment;

    /**
     * Broadcast Container
     *
     * @var array
     */
    protected array $broadcastDataContainer = [];

    /**
     * @param  DeploymentTask  $deploymentTask
     * @param  Deployment  $deployment
     */
    public function __construct(DeploymentTask $deploymentTask, Deployment $deployment)
    {
        parent::__construct();

        $this->deploymentTask = $deploymentTask;
        $this->deployment = $deployment;

        $this->broadcastDataContainer = self::getBroadcastDataContainer($deploymentTask, $deployment);
    }

    /**
     * @param  DeploymentTask|null  $deploymentTask
     * @param  Deployment|null  $deployment
     * @return array
     */
    public static function getBroadcastDataContainer(
        ?DeploymentTask $deploymentTask = null,
        ?Deployment $deployment = null
    ): array {
        $container = [
            'deployment_id'                       => $deployment ? $deployment->getKey() : 0,
            'deployment_label'                    => $deployment ? $deployment->label : '',
            'deployment_task_id'                  => $deploymentTask ? $deploymentTask->getKey() : 0,
            'deployment_task_label'               => $deploymentTask ? $deploymentTask->label : '',
            'deployment_task_total_command_count' => 0,
            'deployment_task_command'             => '',
            'step'                                => 1,
            'steps_total'                         => 1,
            'message'                             => '',
            'timestamp'                           => date('H:i:s'),
        ];

        return $container;
    }

    /**
     * @param  string  $message
     * @param  bool  $updateTimestamp
     * @return mixed
     */
    public function dispatchBroadcast(string $message = '', bool $updateTimestamp = true): mixed
    {
        if ($message) {
            $this->broadcastDataContainer['message'] = $message;
        }
        if ($updateTimestamp) {
            $this->broadcastDataContainer['timestamp'] = date('H:i:s');
        }
        return DeploymentConsole::dispatch($this->broadcastDataContainer);
    }

    /**
     * @param  array  $commandParts
     * @param  array  $commandData
     * @return bool
     */
    public function run(array $commandParts, array $commandData): bool
    {
        return $this->runOrSimulateColonMethod($commandParts, $commandData, 'run');
    }

    /**
     * @param  array  $commandParts
     * @param  array  $commandData
     * @return bool
     */
    public function simulate(array $commandParts, array $commandData): bool
    {
        return $this->runOrSimulateColonMethod($commandParts, $commandData, 'simulate');
    }

    /**
     * Try to find method_x() by command "cmd_x:method_x" ot something like this.
     *
     * @param  array  $commandParts
     * @param  array  $commandData
     * @param  string  $runMethodPrefix
     * @return bool
     */
    public function runOrSimulateColonMethod(
        array $commandParts,
        array $commandData,
        string $runMethodPrefix = 'run'
    ): bool {
        $thisName = get_class($this);
        if (!($commandMethodName = $commandParts[1])) {
            $this->error(sprintf("Missing '%s' command", $thisName), [$commandParts, $commandData]);
        }

        $this->debug(sprintf("Executing '%s' command: %s", $thisName, $commandMethodName));
        $this->broadcastDataContainer['deployment_task_command'] = $thisName;


        $commandMethodName = $runMethodPrefix.ucfirst(Str::camel($commandMethodName));
        if (method_exists($this, $commandMethodName)) {
            return $this->$commandMethodName($commandData);
        } else {
            $this->error(sprintf("Missing method '%s'", $commandMethodName));
            return false;
        }
    }

    /**
     * Merging the vars of deployment, task and task pivot.
     *
     * @return array
     */
    protected function getInheritedVarList(): array
    {
        $v1 = $this->deployment->var_list ?? [];
        $v2 = $this->deploymentTask->var_list ?? [];
        $varList = SystemHelper::arrayMergeRecursiveDistinct($v1, $v2);

        $v2 = $this->deploymentTask->pivot->var_list ?? [];
        $varList = SystemHelper::arrayMergeRecursiveDistinct($varList, $v2);
        return $varList;
    }

    /**
     * Parsing all placeholders in $commandData.
     *
     * @param  array  $commandData
     * @return array
     */
    public function parseCommandData(array $commandData): array
    {
        //        $this->debug('Current task: '.print_r($this->deploymentTask->toArray(), true));

        /** @var DeploymentTaskParserService $parser */
        $parser = app(DeploymentTaskParserService::class);

        $varList = $this->getInheritedVarList();
        $placeHolders = [
            'task'   => [
                'parameters' => [],
                'callback'   => function (array $placeholderParameters, array $parameters, array $recursiveData) {

                    // placeholder parameter can be var or property ...
                    if ($property = data_get($placeholderParameters, "name",
                        data_get($placeholderParameters, "property", ''))) {
                        return (string) data_get($this->deploymentTask, $property, '');
                    }

                    return '';
                },
            ],
            'env'    => [
                'parameters' => [],
                'callback'   => function (array $placeholderParameters, array $parameters, array $recursiveData) use (
                    $parser
                ) {
                    $property = data_get($placeholderParameters, "name", '');
                    if (($parser->allowThrowExceptions) && (!$property || (getenv($property) === false))) {
                        throw new Exception(sprintf("Unknown env var: %s", $property));
                    }
                    return env($property, '');
                },
            ],
            'config' => [
                'parameters' => [],
                'callback'   => function (array $placeholderParameters, array $parameters, array $recursiveData) use (
                    $parser
                ) {
                    $property = data_get($placeholderParameters, "name", '');
                    if (($parser->allowThrowExceptions) && (!$property || !config()->has($property))) {
                        throw new Exception(sprintf("Unknown config entry: %s", $property));
                    }
                    return config($property, '');
                },
            ],
            'var'    => [
                'parameters' => [],
                // Its important &$varList is a reference because it's modified below!
                'callback'   => function (array $phParams, array $params, array $rData) use ($parser, &$varList) {
                    $property = data_get($phParams, "name", '');
                    if (($parser->allowThrowExceptions) && (!$property || !Arr::exists($varList, $property))) {
                        throw new Exception(sprintf("Unknown var name: %s", $property));
                    }
                    return data_get($varList, $property, '');
                },
            ],
        ];

        $parser->setPlaceholders($placeHolders);

        // At first parse the vars itself which is used in placeholders defined above ...
        $varList = $parser->parseArray($varList);
        //        $this->debug('parsed var_list: '.print_r($varList, true));

        // Parse the commands now ...
        $result = $parser->parseArray($commandData);
//        $this->debug('parsed command data: '.print_r($result, true));
        return $result;
    }

    /**
     * @param  array  $parameters
     * @return \Illuminate\Validation\Validator|null
     */
    protected function validateParameters(array $parameters): ?\Illuminate\Validation\Validator
    {
        try {
            $validator = Validator::make($parameters, $this->validateParameters);
            if ($validator->errors()->any()) {
                $this->error("Validator errors: ", $validator->errors()->toArray());
            } else {
                $this->debug("Validation OK!");
                $this->debug(print_r($validator->validated(), true));
            }

            return $validator;

        } catch (Exception $ex) {
            $this->error($ex->getMessage());
            $this->error($ex->getTraceAsString());
        }

        return null;
    }

}