<?php
//
//namespace Modules\KlaraDeployment\Services;
//
//use CzProject\GitPhp\Git;
//use CzProject\GitPhp\GitRepository;
//use Illuminate\Support\Facades\Log;
//
//class GitService
//{
//    /**
//     * @var GitRepository|null
//     */
//    protected ?GitRepository $gitRepository = null;
//
//    /**
//     *
//     */
//    public function __construct()
//    {
//    }
//
//    /**
//     * @param  string  $path
//     * @param  bool  $fetch
//     * @return bool
//     */
//    public function openRepository(string $path, bool $fetch = false): bool
//    {
//        $git = new Git;
//        $this->gitRepository = $git->open($path);
//
//        if ($fetch && $this->gitRepository) {
//            $this->repositoryFetch();
//        }
//
//        return !!$this->gitRepository;
//    }
//
//    /**
//     * @param  string  $srcUrl
//     * @param  string  $destPath
//     *
//     * @return bool
//     */
//    public function createRepository(string $srcUrl, string $destPath): bool
//    {
//        $git = new Git;
//
//        try {
//            $this->gitRepository = $git->cloneRepository($srcUrl, $destPath);
//        } catch (\Exception $ex) {
//            Log::error($ex->getMessage(), [__METHOD__]);
//            return false;
//        }
//
//        return !!$this->gitRepository;
//    }
//
//    /**
//     * @return GitRepository|null
//     */
//    public function repositoryPull(): ?GitRepository
//    {
//        try {
//            return $this->gitRepository->pull();
//        } catch (\Exception $ex) {
//            Log::error($ex->getMessage(), [__METHOD__]);
//            return null;
//        }
//    }
//
//    /**
//     * @return GitRepository|null
//     */
//    public function repositoryFetch(): ?GitRepository
//    {
//        try {
//            return $this->gitRepository->fetch();
//        } catch (\Exception $ex) {
//            Log::error($ex->getMessage(), [__METHOD__]);
//            return null;
//        }
//    }
//
//    /**
//     * @param  string  $branchName
//     * @return bool
//     */
//    public function ensureBranch(string $branchName): bool
//    {
//        try {
//            if ($this->gitRepository->getCurrentBranchName() !== $branchName) {
////                Log::debug(sprintf("Switching branch to %s", $branchName));
//                $this->gitRepository->checkout($branchName);
//            }
//        } catch (\Exception $ex) {
//            Log::error($ex->getMessage(), [__METHOD__]);
//            return false;
//        }
//
//        return true;
//    }
//
//}
