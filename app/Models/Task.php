<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task',
        'date',
        'time',
        'created_at',
        'user_id'
    ];

    public function users(): HasMany 
    {
        return $this->hasMany(User::class, 'user_id');
    }
}