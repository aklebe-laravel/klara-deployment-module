<?php

namespace Modules\KlaraDeployment\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\DeployEnv\app\Events\ImportContent;
use Modules\WebsiteBase\app\Models\MediaItem;
use Modules\WebsiteBase\app\Services\MediaService;
use Modules\WebsiteBase\app\Services\SendNotificationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Telegram\Bot\Exceptions\TelegramSDKException;

class MediaItemImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MediaItem
     */
    public MediaItem $mediaItem;

    protected array $importObjectTypeMap = [
        MediaItem::OBJECT_TYPE_IMPORT_PRODUCT  => 'product',
        MediaItem::OBJECT_TYPE_IMPORT_USER     => 'user',
        MediaItem::OBJECT_TYPE_IMPORT_CATEGORY => 'category',
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MediaItem $mediaItem)
    {
        $this->mediaItem = $mediaItem->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TelegramSDKException
     */
    public function handle(): void
    {
        // Log::debug(sprintf("Handle offer (%s), status: %s.", $this->offer->id, $this->offer->status), [__METHOD__]);

        // Checking the new status ...
        switch ($this->mediaItem->media_type) {

            case MediaItem::MEDIA_TYPE_IMPORT:
                {
                    Log::debug("Importing media item {$this->mediaItem->id}", [__METHOD__]);

                    if ($type = data_get($this->importObjectTypeMap, $this->mediaItem->object_type)) {

                        /** @var MediaService $mediaService */
                        $mediaService = app(MediaService::class);

                        if (($importFile = $mediaService->getMediaItemPath($this->mediaItem)) && (is_file($importFile))) {

                            $importFilePathInfo = pathinfo($importFile);
                            // starting the import
                            ImportContent::dispatch($type, $importFilePathInfo, $this->mediaItem->user_id);

                            // send a notification
                            /** @var SendNotificationService $sendNotificationService */
                            $sendNotificationService = app(SendNotificationService::class);
                            $sendNotificationService->sendNotificationConcern('market_media_item_import', $this->mediaItem->user_id, [
                                'media_item' => $this->mediaItem,
                            ]);

                        } else {
                            Log::error("Import media item {$this->mediaItem->id} failed. Invalid file.");
                        }

                    } else {
                        Log::error("Import media item {$this->mediaItem->id} failed. Invalid object type.");
                    }
                }
                break;

            default:
                break;

        }

    }
}
