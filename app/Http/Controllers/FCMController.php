<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FCMController extends Controller
{
    public function enviarNotificacion(Request $request)
    {
        $token = $request->token;
        $titulo = $request->titulo;
        $mensaje = $request->mensaje;

        $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');

        $data = [
            "to" => $token,
            "notification" => [
                "title" => $titulo,
                "body" => $mensaje,
                "sound" => "default"
            ]
        ];

        $response = Http::withHeaders([
            "Autorization" => "key=$SERVER_API_KEY",
            "Content-Type" => "application/json"
        ])->post('https://fcm.googleapis.com/fcm/send', $data);

        return response()->json(['status' => $response->status(), 'data' => $response->json()]);
    }
}
