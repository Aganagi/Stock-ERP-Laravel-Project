<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'company',
        'phone',
        'image',
        'user_id'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function hasOrder()
    {
        $order = Order::where('client_id', $this->id)->where('status', 'confirmed')->first();

        return $order ? true : false;
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
