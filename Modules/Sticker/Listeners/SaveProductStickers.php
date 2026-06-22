<?php

namespace Modules\Sticker\Listeners;

use Modules\Product\Entities\Product;
use Modules\Sticker\Entities\Sticker;
use Illuminate\Support\Collection;

class SaveProductStickers
{
    /**
     * Handle the event.
     *
     * @param Product $product
     *
     * @return void
     */
    public function handle(Product $product)
    {
        if (!$this->shouldSync()) {
            return;
        }

        $product->stickers()->sync(
            $this->getSyncData()
        );
    }

    /**
     * Determine whether product stickers should be synchronized.
     *
     * @return bool
     */
    private function shouldSync(): bool
    {
        return request()->routeIs(
                'admin.products.store',
                'admin.products.update'
            ) && request('_sync_stickers') === '1';
    }

    /**
     * Prepare sticker pivot data.
     *
     * @return array
     */
    private function getSyncData(): array
    {
        $stickerIds = $this->getStickerIds();

        if ($stickerIds->isEmpty()) {
            return [];
        }

        $sortOrders = Sticker::withoutGlobalScope('active')
            ->whereIn('id', $stickerIds)
            ->pluck('sort_order', 'id');

        return $stickerIds
            ->mapWithKeys(function ($stickerId) use ($sortOrders) {
                return [
                    $stickerId => [
                        'sort_order' => (int)$sortOrders->get(
                            $stickerId,
                            0
                        ),
                    ],
                ];
            })
            ->all();
    }

    /**
     * Get unique sticker ids from the request.
     *
     * @return Collection
     */
    private function getStickerIds(): Collection
    {
        return collect(request('stickers', []))
            ->filter(function ($stickerId) {
                return is_numeric($stickerId)
                    && (int)$stickerId > 0;
            })
            ->map(function ($stickerId) {
                return (int)$stickerId;
            })
            ->unique()
            ->values();
    }
}
