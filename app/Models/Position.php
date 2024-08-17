<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitud',
        'longitud',
        'status',
        'user_id',
    ];

    /**
     * Relación con el modelo User.
     * Un Position pertenece a un User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
