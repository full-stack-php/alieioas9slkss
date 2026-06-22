<?php

namespace Modules\Support\Entities;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Admin\Ui\AdminTable;
use Modules\Support\Admin\ExportTable;
use Modules\Support\Eloquent\Model; // Используем вашу базовую модель
use Illuminate\Database\Eloquent\Relations\HasMany;

class Export extends Model
{

    protected $table = 'export_profiles';

    protected $fillable = [
        'name',
        'entity',
        'format',
        'file_name',
        'settings',
        'columns',
        'filters',
        'sortings',
        'is_active',
        'cron_schedule',
        'locale',
    ];


    protected $casts = [
        'settings'  => 'array',
        'columns'   => 'array',
        'filters'   => 'array',
        'sortings'  => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        parent::booted();

        static::updating(function ($export) {
            if (request()->isMethod('put') || request()->isMethod('patch') || request()->isMethod('post')) {
                if (!request()->has('filters')) {
                    $export->filters = [];
                }
                if (!request()->has('columns')) {
                    $export->columns = [];
                }
            }
        });
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ExportLog::class, 'profile_id')->orderByDesc('created_at');
    }

    public function latestLog()
    {
        return $this->hasOne(ExportLog::class, 'profile_id')->latestOfMany();
    }

    public function table(Request $request): ExportTable
    {
        return new ExportTable($this->newQuery()->withoutGlobalScope('active'));
    }
}
