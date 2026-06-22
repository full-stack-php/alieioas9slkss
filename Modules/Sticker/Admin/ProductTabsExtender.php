<?php

namespace Modules\Sticker\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;
use Modules\Product\Entities\Product;
use Modules\Sticker\Entities\Sticker;
use Illuminate\Support\Collection;

class ProductTabsExtender
{
    /**
     * Extend product tabs.
     *
     * @param Tabs $tabs
     *
     * @return void
     */
    public function extend(Tabs $tabs)
    {
        $tabs->group('basic_information')
            ->add($this->stickers());
    }

    /**
     * Product stickers tab.
     *
     * @return Tab|null
     */
    private function stickers()
    {
        if (!auth()->user()->hasAccess('admin.stickers.index')) {
            return;
        }

        return tap(
            new Tab(
                'stickers',
                trans('sticker::stickers.tabs.product.stickers')
            ),
            function (Tab $tab) {
                $tab->weight(25);

                $tab->fields([
                    'stickers',
                    'stickers.*',
                ]);

                $tab->view(function ($data) {
                    $selectedStickerIds = $this->getSelectedStickerIds(
                        $data['product']
                    );

                    return view(
                        'sticker::admin.products.tabs.stickers',
                        [
                            'stickerOptions' => $this->getStickerOptions(
                                $selectedStickerIds
                            ),
                            'selectedStickerIds' => $selectedStickerIds,
                        ]
                    );
                });
            }
        );
    }

    /**
     * Get selected sticker ids.
     *
     * @param Product $product
     *
     * @return Collection
     */
    private function getSelectedStickerIds(Product $product): Collection
    {
        $old = old('stickers');

        if (!is_null($old)) {
            return collect($old)
                ->filter()
                ->map(function ($stickerId) {
                    return (int)$stickerId;
                })
                ->unique()
                ->values();
        }

        if (!$product->exists) {
            return collect();
        }

        return $product->stickers()
            ->withoutGlobalScope('active')
            ->pluck('stickers.id')
            ->map(function ($stickerId) {
                return (int)$stickerId;
            })
            ->values();
    }

    /**
     * Get available sticker options.
     *
     * Active stickers and already selected inactive stickers
     * are displayed in the product form.
     *
     * @param Collection $selectedStickerIds
     *
     * @return Collection
     */
    private function getStickerOptions(
        Collection $selectedStickerIds
    ): Collection {
        return Sticker::withoutGlobalScope('active')
            ->where(function ($query) use ($selectedStickerIds) {
                $query->where('is_active', true);

                if ($selectedStickerIds->isNotEmpty()) {
                    $query->orWhereIn(
                        'id',
                        $selectedStickerIds
                    );
                }
            })
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (Sticker $sticker) {
                return [
                    $sticker->id => $this->getStickerLabel(
                        $sticker
                    ),
                ];
            });
    }

    /**
     * Get sticker option label.
     *
     * @param Sticker $sticker
     *
     * @return string
     */
    private function getStickerLabel(Sticker $sticker): string
    {
        $name = $sticker->name
            ?: $sticker->image_alt
                ?: "#{$sticker->id}";

        $type = trans(
            "sticker::stickers.form.sticker_types.{$sticker->type}"
        );

        $status = $sticker->is_active
            ? ''
            : ' — ' . trans('admin::admin.table.inactive');

        return "{$name} ({$type}){$status}";
    }
}
