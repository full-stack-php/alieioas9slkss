<?php

namespace Modules\EmailTemplate\Entities;

use Illuminate\Http\Request;
use Modules\Admin\Ui\AdminTable;
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
        'header',
        'body',
        'footer',
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
            ->where('type', $type)
            ->where('recipient', $recipient)
            ->orderBy('sort_order')
            ->orderBy('id');
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
        if (empty($this->status_key)) {
            return trans('emailtemplate::email_templates.form.any_status');
        }

        return (string) $this->status_key;
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
