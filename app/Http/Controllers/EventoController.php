<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'partido_id'=>'required|exists:partidos,id',
            'jugador_id'=>'required|exists:jugadores,id',
            'tipo'=>'required|in:gol,amarilla,roja',
        ]);

        $evento = new \App\Models\Estadistica();
        $evento->partido_id = $request->partido_id;
        $evento->jugador_id = $request->jugador_id;
        $evento->save();

        return response()->json(['message'=>'Evento Registrado'], 201);
    }
}
