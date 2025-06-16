<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jugador;

class EstadisticaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'partido_id'=> 'required|exists:partidos,id',
            'jugador_id'=> 'required|exists:jugadores,id',
            'tipo'=> 'required|in:gol,amarilla,roja',
        ]);

        $estadistica = \App\Models\Estadistica::create($request->only('partido_id', 'jugador_id', 'tipo'));

        return response()->json($estadistica, 201);
    }

    public function porPartido($partido_id)
    {
        $estadisticas = \App\Models\Estadistica::with('jugador')->where('partido_id', $partido_id)->get()->groupBy('tipo');

        return response()->json($estadisticas);
    }

    public function rankingGoleadores()
    {
        $ranking = DB::table('estadisticas')->select('jugador_id', DB::raw('COUNT(*) as goles'))->where('tipo','gol')->groupBy('jugador_id')->orderByDesc('goles')->limit(5)->get();

        $jugadores = Jugador::whereIn('id', $ranking->pluck('jugador_id'))->get()->keyBy('id');

        $data = $ranking->map(function($item) use ($jugadores){
            $jugador = $jugadores[$item->jugador_id];
            return[
                'id'=>$jugador->id,
                'nombre'=>$jugador->nombre,
                'goles'=>$item->goles,
            ];
        });

        return response()->json($data);
    }

    public function ranking()
    {
        $ranking = \App\Models\Estadistica::select('jugador_id', DB::raw('count(*) as goles'))->where('tipo', 'gol')->groupBy('jugador_id')->orderByDesc('goles')->with('jugador')->get()
        ->map(function($r){
            return[
                'jugador'=>$r->jugador->nombre,
                'goles'=>$r->goles,
            ];
        });

        return response()->json($ranking);
    }
}
