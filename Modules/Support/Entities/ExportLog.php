<?php

namespace Modules\Support\Entities;

use Modules\Support\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportLog extends Model
{
    protected $table = 'export_logs';

    protected $fillable = [
        'profile_id',
        'status',
        'file_path',
        'total_rows',
        'generated_at',
        'execution_time',
        'memory_usage',
        'error_message',
    ];

    protected $casts = [
        'generated_at'   => 'datetime',
        'total_rows'     => 'integer',
        'execution_time' => 'integer',
        'memory_usage'   => 'integer',
    ];


    public function profile(): BelongsTo
    {
        return $this->belongsTo(Export::class, 'profile_id');
    }

    public function getMemoryUsageMbAttribute(): float
    {
        return round($this->memory_usage / 1024 / 1024, 2);
    }
}
