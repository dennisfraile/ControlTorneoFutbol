<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EventoPartido;

class EventoPartidoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'partido_id' => 'required|exists:partidos,id',
            'jugador_id' => 'nullable|exists:jugadors,id',
            'equipo_id' => 'required|exists:equipos,id',
            'tipo' => 'required|in:gol,amarilla,roja,cambio',
            'descripcion' => 'nullable|string',
            'minuto' => 'nullable|integer'
        ]);

        $evento = EventoPartido::create($request->all());

        // Notificar representantes
        $this->notificarRepresentantes($evento);

        return response()->json(['evento' => $evento], 201);

    }

    protected function notificarRepresentantes($evento)
    {
        $representantes = User::where('rol', 'representante')
            ->whereNotNull('fcm_token')
            ->whereHas('equipos', function ($q) use ($evento) {
                $q->where('id', '!=', $evento->equipo_id);
            })->get();

        foreach ($representantes as $rep) {
            $this->enviarNotificacionFCM(
                $rep->fcm_token,
                "Evento en partido",
                "Se registró un {$evento->tipo} en el partido."
            );
        }

    }

    protected function enviarNotificacionFCM($token, $titulo, $mensaje)
    {
        $SERVER_API_KEY = env('FIREBASE_SERVER_KEY');

        $data = [
            "to" => $token,
            "notification" => [
                "title" => $titulo,
                "body" => $mensaje,
                "sound" => "default"
            ]
        ];

        Http::withHeaders([
            "Authorization" => "key=$SERVER_API_KEY",
            "Content-Type" => "application/json"
        ])->post('https://fcm.googleapis.com/fcm/send', $data);
    }
    public function registrarEvento(Request $request)
    {
        // Crear evento (goles, tarjetas...)
        $evento = EventoPartido::create($request->all());

        // Buscar representantes del equipo contrario para notificar
        $representantes = User::where('rol', 'representante')
            ->whereNotNull('fcm_token')
            ->whereHas('equipos', function ($q) use ($evento) {
                $q->where('id', '!=', $evento->equipo_id); // contrario
            })->get();

        foreach ($representantes as $rep) {
            $this->enviarNotificacionFCM(
                $rep->fcm_token,
                'Nuevo evento en el partido',
                "Se registró un {$evento->tipo} para el equipo contrario"
            );
        }

        return response()->json($evento);
    }
}
