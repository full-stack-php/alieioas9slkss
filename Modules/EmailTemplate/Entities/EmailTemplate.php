<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Http\Request;
use Modules\Admin\Ui\AdminTable;
use Modules\Order\Entities\OrderStatus;
use Modules\Support\Eloquent\Model;
use Modules\Support\Eloquent\Translatable;
use Modules\EmailTemplate\Services\EmailTemplateType;

class EmailTemplate extends Model
{
    use Translatable;

    protected $with = ['translations'];

    protected $fillable = [
        'type',
        'recipient',
        'status_key',
        'is_active',
        'show_product_image',
        'product_image_max_width',
        'product_image_max_height',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_product_image' => 'boolean',
        'product_image_max_width' => 'integer',
        'product_image_max_height' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $translatedAttributes = [
        'name',
        'subject',
        'content',
    ];

    protected $appends = [
        'type_label',
        'recipient_label',
        'status_key_label',
    ];

    protected static function booted()
    {
        static::addActiveGlobalScope();
    }

    public function scopeForMail($query, string $type, string $recipient)
    {
        return $query
            ->withoutGlobalScope('active')
            ->where('is_active', true)
            ->where('type', $type)
            ->where('recipient', $recipient)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function getStatusKeysAttribute(): array
    {
        if (empty($this->status_key)) {
            return [];
        }

        $decoded = json_decode($this->status_key, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return collect($decoded)
                ->filter(fn ($status) => $status !== null && $status !== '')
                ->map(fn ($status) => (string) $status)
                ->unique()
                ->values()
                ->toArray();
        }

        return [(string) $this->status_key];
    }

    public function setStatusKeyAttribute($value): void
    {
        $statuses = $this->normalizeStatusKeys($value);

        $this->attributes['status_key'] = empty($statuses)
            ? null
            : json_encode($statuses);
    }

    public function matchesStatusKey(?string $statusKey): bool
    {
        $statuses = $this->status_keys;

        if (empty($statuses)) {
            return is_null($statusKey) || $statusKey === '';
        }

        if (is_null($statusKey) || $statusKey === '') {
            return false;
        }

        return in_array((string) $statusKey, $statuses, true);
    }

    public function appliesToStatusKey(?string $statusKey): bool
    {
        $statuses = $this->status_keys;

        if (empty($statuses)) {
            return true;
        }

        if (is_null($statusKey) || $statusKey === '') {
            return false;
        }

        return in_array((string) $statusKey, $statuses, true);
    }

    private function normalizeStatusKeys($value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = [$value];
            }
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return collect($value)
            ->filter(fn ($status) => $status !== null && $status !== '')
            ->map(fn ($status) => (string) $status)
            ->unique()
            ->values()
            ->toArray();
    }

    public function getTypeLabelAttribute(): string
    {
        return EmailTemplateType::label($this->type);
    }

    public function getRecipientLabelAttribute(): string
    {
        return EmailTemplateType::recipientLabel($this->recipient);
    }

    public function getStatusKeyLabelAttribute(): string
    {
        $statuses = $this->status_keys;

        if (empty($statuses)) {
            return trans('emailtemplate::email_templates.form.any_status');
        }

        $statusLabels = OrderStatus::list();

        return collect($statuses)
            ->map(fn ($status) => $statusLabels[$status] ?? $status)
            ->filter()
            ->implode(', ');
    }

    public function table(Request $request)
    {
        $query = $this->newQuery()->withoutGlobalScope('active');

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('recipient')) {
            $query->where('recipient', $request->get('recipient'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        return new AdminTable($query);
    }
}
