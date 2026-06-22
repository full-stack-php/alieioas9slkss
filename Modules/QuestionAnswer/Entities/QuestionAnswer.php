<?php

namespace Modules\QuestionAnswer\Entities;

use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Support\Eloquent\Model;
use Modules\Product\Entities\Product;
use Modules\QuestionAnswer\Admin\QuestionAnswerTable;

class QuestionAnswer extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_approved' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status', 'created_at_formatted'];


    protected $table = 'questions_answers';


    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('approved', function ($query) {
            $query->where('is_approved', true);
        });
    }


    public function getStatusAttribute()
    {
        return $this->status();
    }


    public function status()
    {
        if ($this->is_approved) {
            return trans('questionanswer::statuses.approved');
        }

        return trans('questionanswer::statuses.unapproved');
    }


    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->toFormattedDateString();
    }


    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }


    public function asker()
    {
        return $this->belongsTo(User::class, 'asker_id');
    }


    /**
     * Get table data for the resource
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function table(Request $request)
    {
        $query = static::withoutGlobalScope('approved')
            ->with(['product' => function ($query) {
                $query->withoutGlobalScope('active');
            }])
            ->when($request->productId, function ($query) use ($request) {
                return $query->where('product_id', $request->productId);
            });

        return new QuestionAnswerTable($query);
    }
}
