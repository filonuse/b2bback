<?php

namespace App\Models;

use App\Events\ReviewCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Review
 * @package App\Models
 */
class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_user_id',
        'review',
        'estimate',
        'reviewable_id',
        'reviewable_type',
    ];

    protected $dates = ['deleted_at'];

    protected $dispatchesEvents = [
        'created' => ReviewCreated::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function reviewable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'from_user_id');
    }
}
