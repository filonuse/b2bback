<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = ['message'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipients()
    {
        return $this
            ->belongsToMany('App\Models\User', 'user_messages', 'message_id', 'to_user_id')
            ->withPivot('from_user_id', 'read_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sender()
    {
        return $this
            ->belongsToMany('App\Models\User', 'user_messages', 'message_id', 'from_user_id')
            ->withPivot('to_user_id', 'read_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function users()
    {
        return $this->hasOne('App\Models\Relationships\UserMessage');
    }
}
