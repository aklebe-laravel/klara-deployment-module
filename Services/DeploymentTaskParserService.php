<?php

namespace Modules\KlaraDeployment\Services;

use Modules\SystemBase\Services\ParserService;

class DeploymentTaskParserService extends ParserService
{
    /**
     *
     * @return void
     */
    protected function init(): void
    {
        //        $this->placeholders = [
        //            'code' => [
        //                'parameters' => [],
        //                'callback'   => function (array $placeholderParameters, array $parameters, array $recursiveData) {
        //                    $task = data_get($parameters, 'deployment_task');
        ////                    Log::debug('CODE DEBUG:'.print_r($parameters, true));
        ////                    Log::debug('CODE DEBUG:'.print_r($placeholderParameters, true));
        //                    return ($task) ? $task->code : 'xxx';
        //                },
        //            ],
        //        ];
    }
}