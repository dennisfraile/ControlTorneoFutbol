<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partido;

class PartidoController extends Controller
{
    public function index()
    {
        return Partido::with(['equipoLocal', 'equipoVisitante'])->orderBy('fecha', 'desc')->get()->map(function($partido){
            return[
                'id' => $partido->id,
                'fecha'=>$partido->fecha,
                'equipo_local'=>$partido->equipoLocal->nombre,
                'equipo_visitante'=>$partido->equipo_visitante->nombre,
            ];
        });
    }

    public function eventos($id)
    {
        $eventos = \App\Models\Estadistica::with('jugador')->where('partido_id', $id)->orderBy('created_at')->get()->map
        (function($evento){
            return[
                'jugador'=>$evento->jugador->nombre,
                'tipo'=>$evento->tipo,
                'minuto'=>$evento->created_at->format('H:i'),
            ];
        });

        return response()->json($eventos);

    }
}
