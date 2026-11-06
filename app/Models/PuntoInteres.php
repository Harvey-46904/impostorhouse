<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntoInteres extends Model
{
    protected $table = 'punto_interes';
     protected $fillable = [
        'lugar_id',
        'nombre',
        'x',
        'y',
        'ancho',
        'alto'
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class);
    }
}
