<?php

namespace Modules\Category\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Entities\Category;

class CategoryDescendantIds
{
    public function bySlug(string $slug): array
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category || !$category->exists) {
            return [];
        }

        return $this->forCategory($category);
    }

    public function forCategory(Category $category): array
    {
        return Cache::tags('categories')->rememberForever(
            'category_descendant_ids:' . $category->id,
            function () use ($category) {
                $ids = [(int) $category->id];

                $this->appendChildrenIds((int) $category->id, $ids);

                return collect($ids)
                    ->unique()
                    ->values()
                    ->all();
            }
        );
    }

    private function appendChildrenIds(int $parentId, array &$ids): void
    {
        $children = Category::withoutGlobalScopes()
            ->where('parent_id', $parentId)
            ->pluck('id');

        foreach ($children as $childId) {
            $childId = (int) $childId;

            $ids[] = $childId;

            $this->appendChildrenIds($childId, $ids);
        }
    }
}
