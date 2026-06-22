<?php

namespace Modules\Meta\Eloquent;

use Modules\Meta\Entities\MetaData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasMetaData
{
    /**
     * The "booting" method of the trait.
     *
     * @return void
     */
    public static function bootHasMetaData()
    {
        static::saved(function ($entity) {
            $entity->saveMetaData(request('meta', []));
        });

        static::deleted(function ($entity) {
            if ($entity->meta()->exists()) {
                $entity->meta->delete();
            }
        });
    }


    /**
     * Save metadata for the entity.
     *
     * @param array $data
     *
     * @return Model
     */
    public function saveMetaData($data = [])
    {
        $metaEntity = $this->meta()->firstOrNew([
            'entity_id' => $this->id,
            'entity_type' => $this->getMorphClass(),
        ]);

        $metaEntity->fill($data)->save();

    }


    /**
     * Get the meta for the entity.
     *
     * @return MorphToMany
     */
    public function meta()
    {
        return $this->morphOne(MetaData::class, 'entity')->with([
            'translations' => function ($query) {
                $query->withoutGlobalScope('locale');
            }
        ]);
    }
}
