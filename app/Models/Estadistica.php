<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estadistica extends Model
{
    public function jugador(){
        return $this->belongsTo(Jugador::class);
    }

    public function partido(){
        return $this->belongsTo(Partido::class);
    }
}
