<?php

namespace Modules\Contact\Entities;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Contact\Admin\ContactSubmissionTable;
use Modules\Support\Eloquent\Model;
use Modules\User\Entities\User;

class ContactSubmission extends Model
{
    protected $table = 'contact_submissions';

    protected $fillable = [
        'type',
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'topic',
        'preferred_call_at',
        'source_url',
        'ip_address',
        'user_agent',
        'read_at',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'preferred_call_at' => 'datetime',
        'read_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    public function getIsProcessedAttribute(): bool
    {
        return !is_null($this->processed_at);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'callback' => trans('contact::contact_submissions.types.callback'),
            'contact' => trans('contact::contact_submissions.types.contact'),
            default => trans('contact::contact_submissions.types.unknown'),
        };
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update([
                'read_at' => now(),
            ]);
        }
    }

    public function markAsProcessed(): void
    {
        $this->update([
            'processed_at' => now(),
            'processed_by' => auth()->id(),
        ]);
    }

    public function markAsUnprocessed(): void
    {
        $this->update([
            'processed_at' => null,
            'processed_by' => null,
        ]);
    }

    public function table(Request $request)
    {
        $query = static::query()
            ->with('processor')
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('read'), function ($query) use ($request) {
                if ($request->input('read') === '1') {
                    $query->whereNotNull('read_at');
                }

                if ($request->input('read') === '0') {
                    $query->whereNull('read_at');
                }
            })
            ->when($request->filled('processed'), function ($query) use ($request) {
                if ($request->input('processed') === '1') {
                    $query->whereNotNull('processed_at');
                }

                if ($request->input('processed') === '0') {
                    $query->whereNull('processed_at');
                }
            })
            ->latest();

        return new ContactSubmissionTable($query);
    }
}
