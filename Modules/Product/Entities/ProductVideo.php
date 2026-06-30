<?php

namespace Modules\Product\Entities;

use Modules\Support\Eloquent\Model;

class ProductVideo extends Model
{
    protected $table = 'product_videos';

    protected $fillable = [
        'product_id',
        'title',
        'url',
        'youtube_id',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'embed_url',
        'thumbnail_url',
    ];

    public static function extractYoutubeId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $patterns = [
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_id) {
            return null;
        }

        return "https://www.youtube.com/embed/{$this->youtube_id}";
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->youtube_id) {
            return null;
        }

        return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function hasYoutubePreview(): bool
    {
        return !empty($this->youtube_id) && !empty($this->thumbnail_url);
    }

    public static function makeFromFormData(array $data): self
    {
        return new self([
            'id' => $data['id'] ?? null,
            'title' => $data['title'] ?? null,
            'url' => $data['url'] ?? null,
            'youtube_id' => self::extractYoutubeId($data['url'] ?? null),
            'is_main' => false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }
}
