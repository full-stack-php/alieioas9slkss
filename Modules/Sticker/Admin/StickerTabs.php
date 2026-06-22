<?php

namespace Modules\Sticker\Admin;

use Modules\Admin\Ui\Tab;
use Modules\Admin\Ui\Tabs;

class StickerTabs extends Tabs
{
    /**
     * Make sticker tabs.
     *
     * @return void
     */
    public function make()
    {
        $this->group(
            'sticker_information',
            trans('sticker::stickers.tabs.group.sticker_information')
        )
            ->active()
            ->add($this->general())
            ->add($this->image());
    }

    /**
     * General sticker information tab.
     *
     * @return Tab
     */
    private function general()
    {
        return tap(
            new Tab(
                'general',
                trans('sticker::stickers.tabs.general')
            ),
            function (Tab $tab) {
                $tab->active();
                $tab->weight(5);

                $tab->fields([
                    'type',
                    'name',
                    'image_alt',
                    'description',
                    'popup_description',
                    'text_color',
                    'background_color',
                    'image_background_color',
                    'sort_order',
                    'is_active',
                ]);

                $tab->view(
                    'sticker::admin.stickers.tabs.general'
                );
            }
        );
    }

    /**
     * Sticker image tab.
     *
     * @return Tab|null
     */
    private function image()
    {
        if (!auth()->user()->hasAccess('admin.media.index')) {
            return;
        }

        return tap(
            new Tab(
                'image',
                trans('sticker::stickers.tabs.image')
            ),
            function (Tab $tab) {
                $tab->weight(10);

                $tab->view(
                    'sticker::admin.stickers.tabs.image'
                );
            }
        );
    }
}
