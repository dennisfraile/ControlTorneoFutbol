<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partido;
use App\Models\Jugador;

class JugadorController extends Controller
{
    public function index()
    {
        return response()->json(\App\Models\Jugador::with('equipo')->get());
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre'=>'required|string|max:255',
            'equipo_id'=>'required|exist:equipos,id',
            'foto'=>'nullable|image|max:2048',
        ]);

        $path = null;
        if($request->hasFile('foto'))
        {
            $path=$request->file('foto')->store('jugadores', 'public');
        }

        Jugador::create([
            'nombre'=>$request->nombre,
            'equipo_id'=>$request->equipo_id,
            'foto'=>$path,
        ]);

        return response()->json(['message' => 'Jugador registrado']);
    }

    public function jugadoresPorPartido($partido_id)
    {
        $partido = Partido::with(['equipoLocal.jugadores', 'equipoVisitantejugadores'])->findOrFail($partido_id);

        $jugadores = $partido->equipoLocal->jugadores->merge($partido->equipoVisitante->jugadores)->map(function($jugador){
            return[
                'id'=>$jugador->id,
                'nombre'=>$jugador->nombre,
                'foto'=>$jugador->foto_url,
            ];
        });

        return response()->json($jugadores);
    }
}
