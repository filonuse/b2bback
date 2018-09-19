<?php

namespace App\Models;


use App\Traits\ReviewRelation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes, ReviewRelation;

    protected $fillable = [
        'name',
        'legal_name',
        'email',
        'password',
        'phone',
        'official_data',
        'requisites',
        'banned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    /*
    | ----------------------------------------
    |   Relationships
    | ----------------------------------------
    */

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'users_roles', 'user_id', 'role_id');
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasRole($name)
    {
        return $this->roles()->value('name') == $name;
    }

    /**
     * @return string
     */
    public function roleName()
    {
        return $this->roles()->value('name');
    }

    /**
     * The categories that belong to the user.
     */
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'users_categories', 'user_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blacklist()
    {
        return $this->belongsToMany('App\Models\User', 'blacklist', 'user_id', 'blocked_user_id');
    }

    /**
     * @param $userId
     * @return bool
     */
    public function checkBlacklist($userId)
    {
        return $this->blacklist()
            ->where('blocked_user_id', '=', $userId)->count() ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods()
    {
        return $this->hasMany('App\Models\Goods');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function discounts()
    {
        return $this
            ->belongsToMany('App\Models\Discount', 'discounts', 'provider_id', 'customer_id')
            ->withTimestamps();
    }

    /**
     * @param integer $userId
     * @return integer
     */
    public function discountFromProvider($userId)
    {
        return $this
            ->hasOne('App\Models\Discount', 'customer_id', 'id')
            ->where('provider_id', $userId)->value('discount');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function messages()
    {
        return $this->belongsToMany('App\Models\Message', 'user_messages', 'from_user_id', 'message_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany('App\Models\Setting', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stores()
    {
        return $this->hasMany('App\Models\Store', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany('App\Models\Route', 'user_id');
    }

    /**
     * @param string $role  The role user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(string $role)
    {
        return $this->hasMany('App\Models\Order', strtolower($role) . '_id');
    }
}
