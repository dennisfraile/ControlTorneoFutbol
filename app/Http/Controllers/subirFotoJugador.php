<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class subirFotoJugador extends Controller
{
    public function subirFotoJugador(Request $request)
    {
        $request->validate([
            'foto'=>'required|image|max2048',
        ]);

        $path = $request->file('foto')->store('jugadores','public');

        return response()->json([
            'url'=>asset("storage/$path"),
            'path'=>$path,
        ]);
    }
}
