<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function guardarFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = auth()->user(); // Asumiendo que el usuario ya está autenticado
        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json(['message' => 'Token guardado exitosamente.']);
    }
}
