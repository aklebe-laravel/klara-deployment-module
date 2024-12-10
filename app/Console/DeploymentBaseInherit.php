<?php

namespace Modules\KlaraDeployment\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

/**
 * Just used to extend deployment commands!
 */
class DeploymentBaseInherit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'x';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'x';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return self::FAILURE;
    }

    /**
     * @param string $command
     * @param array $params
     * @param bool $useThisOutput
     *
     * @return int
     */
    protected function startArtisanCall(string $command, array $params = [], bool $useThisOutput = true): int
    {
        $this->output->comment(sprintf("Starting command : %s", $command));
        if (($result = Artisan::call($command, $params, $useThisOutput ? $this->output : null)) !== self::SUCCESS) {
            $this->output->error("Command $command failed in " . __METHOD__);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getArgDeploymentCode(): string
    {
        if (!($env = $this->argument('code'))) {
            //$env = config('app.env', '');
        }

        return $env;
    }

    /**
     * Print out and log the message.
     * If $message is array or object, the output will be formatted.
     *
     * @param mixed $message
     * @param $logLevel
     *
     * @return void
     */
    public function message(mixed $message, $logLevel = LogLevel::DEBUG): void
    {
        if (!is_scalar($message)) {
            $message = print_r($message);
        }

        $this->output->writeln($message);
        Log::log($logLevel, $message);
    }
}
