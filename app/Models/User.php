<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organization',
        'phone',
        'photo',
        'facebook_id',
        'google_id'
    ];

    public $defaultPhoto = 'no-photo.jpg';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function brand(): BelongsTo 
    {
        return $this->belongsTo(Brand::class, 'user_id');
    }

    public function product(): BelongsTo 
    {
        return $this->belongsTo(Product::class, 'user_id');
    }

    public function client(): BelongsTo 
    {
        return $this->belongsTo(Client::class, 'user_id');
    }

    public function order(): BelongsTo 
    {
        return $this->belongsTo(order::class, 'user_id');
    }

    public function task(): BelongsTo 
    {
        return $this->belongsTo(Task::class, 'user_id');
    }
}
