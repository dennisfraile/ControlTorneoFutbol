<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;

class EquipoController extends Controller
{
    public function index()
    {
        return response()->json(Equipo::all());
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre'=>'required|string|max255',
            'logo'=>'nullable|image|max:2048',
        ]);

        $logoPath = $request->hasFile('logo')
            ?
            $request->file('logo')->store('equipos','public')
            :null;


        $equipo = Equipo::create([
            'nombre'=>$request->nombre,
            'logo'=>$logoPath,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Equipo registrado', 'equipo' => $equipo]);
    }
}
