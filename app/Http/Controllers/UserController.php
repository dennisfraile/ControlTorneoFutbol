<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function guardarFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        $user = auth()->user(); // Asumiendo que el usuario ya está autenticado

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $user->fcm_token = $request->fcm_token;

        try {
            $user->save();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al guardar: ' . $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Token guardado exitosamente.']);

    }
}
