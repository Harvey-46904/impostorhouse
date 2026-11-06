<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    protected $fillable = [
    'nombre',
    'numero_impostores',
    'estado',
    'lugar'
];
}
