<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    protected $table = 'lugares';
    protected $fillable = [
        'nombre',
        'imagen'
    ];

    public function puntos()
    {
        return $this->hasMany(PuntoInteres::class);
    }

    public function salas()
    {
        return $this->hasMany(Sala::class, 'lugar');
    }
}
