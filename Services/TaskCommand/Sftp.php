<?php

namespace Modules\KlaraDeployment\Services\TaskCommand;

use Illuminate\Support\Facades\Storage;
use Modules\KlaraDeployment\Models\Deployment;
use Modules\KlaraDeployment\Models\DeploymentTask;
use Modules\SystemBase\Rules\DirectoryRule;
use Modules\SystemBase\Rules\DiskRule;
use Modules\SystemBase\Rules\SubPathRule;

class Sftp extends Base
{
    /**
     * @param  DeploymentTask  $deploymentTask
     * @param  Deployment  $deployment
     */
    public function __construct(DeploymentTask $deploymentTask, Deployment $deployment)
    {
        parent::__construct($deploymentTask, $deployment);

        $this->validateParameters = [
            'src'            => ['required', new DirectoryRule()],
            'dest'           => ['required', new SubPathRule()],
            'disk'           => ['required', new DiskRule()],
            'directory_deep' => ['integer'],
            'whitelist'      => ['array'],
            'blacklist'      => ['array'],
        ];

    }

    /**
     * Executing a git update.
     *
     * @param  array  $commandData
     * @param  bool  $simulate
     * @return bool
     */
    public function runUpload(array $commandData, bool $simulate = false): bool
    {
        $this->debug(__METHOD__, $commandData);

        // Command Parameter Validation ...
        $validator = $this->validateParameters($commandData);
        if ($validator->errors()->any()) {
            // Errors already logged.
            return false;
        }

        $src = data_get($commandData, 'src');
        $dest = data_get($commandData, 'dest');
        $disk = data_get($commandData, 'disk');
        $fileSystem = Storage::disk($disk);
        if (!$fileSystem->exists($dest)) {
            if ((!$fileSystem->makeDirectory($dest)) || (!$fileSystem->exists($dest))) {
                $this->error(sprintf("Missing path: '%s' for disk '%s'", $dest, $disk));
                return false;
            }
        }
        $whitelist = data_get($commandData, 'whitelist', []);
        $blacklist = data_get($commandData, 'blacklist', []);

        // upload files ...
        $filesUploaded = 0;
        app('system_base_file')->runDirectoryFiles($src, function (string $file, array $sourcePathInfo) use (
            $src,
            $dest,
            $fileSystem,
            $simulate,
            &$filesUploaded
        ) {
            if (($fileRelativePart = app('system_base_file')->subPath($file, $src)) !== null) {

                //                    $this->debug(sprintf("Found file in src: %s", $fileRelativePart));
                $uploadPath = app('system_base_file')->getValidPath($dest.'/'.$fileRelativePart);
                $uploadPathInfo = pathinfo($uploadPath);

                // streaming with putFile instead of put() to save resources
                if ($simulate) {
                    $this->debug(sprintf("Simulating file upload: %s", $uploadPath));
                } else {
                    if (!$fileSystem->putFileAs($uploadPathInfo['dirname'], $file, $uploadPathInfo['basename'])) {
                        $this->error(sprintf("Error uploading file : '%s' to '%s'", $file, $uploadPath));
                    }
                    $this->info(sprintf("File successfully uploaded: '%s'", $uploadPath));
                    $filesUploaded++;
                }

            } else {
                $this->error(sprintf("Error in subPath: %s : %s", $file, $src));
            }
        }, data_get($commandData, 'directory_deep', -1), $whitelist, $blacklist, '/');

        $this->info(sprintf("%d files successfully uploaded to '%s'", $filesUploaded, $dest));
        return true;
    }

    /**
     * Simulating a git update.
     *
     * @param  array  $commandData
     * @return bool
     */
    public function simulateUpload(array $commandData): bool
    {
        return $this->runUpload($commandData, true);
    }
}