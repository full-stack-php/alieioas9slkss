<?php

namespace Modules\Faq\Eloquent;

use Modules\Faq\Entities\Faq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFaq
{
    /**
     * The "booting" method of the trait.
     *
     * @return void
     */
    public static function bootHasFaq()
    {
        static::saved(function ($entity) {
            $entity->syncFaqs(request('faqs', []));
        });

        static::deleted(function ($entity) {
            $entity->faqs()->delete();
        });
    }

    /**
     * Sync answer & question for entity.
     *
     * @param array $faqsData
     * @return void
     */
    public function syncFaqs($faqsData = [])
    {
        $this->faqs()->delete();

        if (empty($faqsData)) {
            return;
        }

        $faqsData = array_filter($faqsData, function ($faqData, $indexKey) {
            if (str_contains($indexKey, '__FAQ_INDEX__')) {
                return false;
            }
            return is_array($faqData) && !empty($faqData);
        }, ARRAY_FILTER_USE_BOTH);

        if (empty($faqsData)) {
            return;
        }

        $loopIndex = 0;
        foreach ($faqsData as $faq) {
            if (! is_array($faq) || empty($faq)) {
                continue;
            }

            $newFaq = $this->faqs()->create([
                'position' => $loopIndex,
            ]);

            $loopIndex++;
            $newFaq->fill($faq)->save();
        }
    }

    /**
     * Get all FAQS for entity.
     *
     * @return MorphMany
     */
    public function faqs()
    {
        return $this->morphMany(Faq::class, 'entity')->with([
            'translations' => function ($query) {
                $query->withoutGlobalScope('locale');
            }
        ]);
    }
}
