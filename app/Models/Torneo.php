<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    public function equipos(){
        return $this->hasMany(Equipo::class);
    }

    public function partidos(){
        return $this->hasMany(Partido::class);
    }
}


