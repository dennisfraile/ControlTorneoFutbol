<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\EventoPartido;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Torneo;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\JugadorController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\FCMController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoPartidoController;
use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/equipos',[EquipoController::class, 'store']);
    Route::post('/jugadores', [JugadorController::class, 'store']);
    Route::get('/jugadores/porPartido/{partido_id}', [JugadorController::class, 'jugadoresPorPartido']);
});

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/partidos', [PartidoController::class, 'index']);
    Route::post('/partidos', [PartidoController::class, 'store']);
    Route::post('/estadisticas', [EstadisticaController::class, 'store']);
    Route::get('/estadisticas/partido/{id}', [EstadisticaController::class, 'porPartido']);
    Route::get('/ranking-goleadores', [EstadisticaController::class, 'rankingGoleadores']);
    Route::middleware('auth:sanctum')->get('/ranking/goleadores', [EstadisticaController::class, 'ranking']);
});

Route::middleware('auth:sanctum')->post('/torneo/limpiar', [TorneoController::class, 'limpiar']);
Route::post('/torneos', function (Request $request) {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'categoria' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    ]);

    return Torneo::create($validated);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/eventos', [EventoController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/eventos', [EventoPartidoController::class, 'store']);
});
Route::middleware('auth:sanctum')->get('/partidos/{id}/eventos', [PartidoController::class, 'eventos']);
Route::middleware('auth:sanctum')->get('/torneos', fn() => \App\Models\Torneo::all());

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:representante'])->group(function(){
    Route::post('/jugadores', [JugadorController::class, 'store']);
});
Route::middleware(['auth:sanctum', 'role:representante'])->post('/equipos', [EquipoController::class, 'store']);

Route::get('/partidos/{id}/eventos', function ($id) {
    $eventos = EventoPartido::with('jugador.equipo')
        ->where('partido_id', $id)
        ->orderBy('minuto')
        ->get()
        ->map(function ($evento) {
            return [
                'tipo' => $evento->tipo,
                'minuto' => $evento->minuto,
                'jugador' => $evento->jugador->nombre,
                'equipo' => $evento->jugador->equipo->nombre,
            ];
        });

    return response()->json(['eventos' => $eventos]);
});

Route::get('/partido/{id}/eventos', function ($id) {
    return EventoPartido::where('partido_id', $id)
        ->orderBy('minuto')
        ->get();
})->middleware('auth:sanctum');

Route::get('/ranking-goleadores', function () {
    $goleadores = Jugador::with('equipo')
        ->withCount(['eventos as goles' => function ($q) {
            $q->where('tipo', 'gol');
        }])
        ->orderByDesc('goles')
        ->take(10)
        ->get()
        ->map(function ($jugador) {
            return [
                'nombre' => $jugador->nombre,
                'equipo' => $jugador->equipo->nombre,
                'goles' => $jugador->goles,
            ];
        });

    return response()->json(['goleadores' => $goleadores]);
});

Route::get('/goleadores', function () {
    return DB::table('evento_partidos')
        ->join('jugadors', 'evento_partidos.jugador_id', '=', 'jugadors.id')
        ->select('jugadors.nombre as jugador', DB::raw('count(*) as goles'))
        ->where('tipo', 'gol')
        ->groupBy('jugadors.id')
        ->orderByDesc('goles')
        ->get();
})->middleware('auth:sanctum');

Route::get('/partidos', function () {
    return DB::table('partidos')
        ->join('equipos as local', 'partidos.equipo_local_id', '=', 'local.id')
        ->join('equipos as visita', 'partidos.equipo_visitante_id', '=', 'visita.id')
        ->select('partidos.id', 'partidos.fecha', 'local.nombre as equipo_local', 'visita.nombre as equipo_visitante')
        ->get();
})->middleware('auth:sanctum');

Route::get('/partidos/{id}/jugadores', function ($id) {
    $partido = Partido::with(['equipoLocal.jugadores', 'equipoVisitante.jugadores'])->findOrFail($id);

    $jugadores = collect()
        ->merge($partido->equipoLocal->jugadores)
        ->merge($partido->equipoVisitante->jugadores)
        ->map(function ($jugador) {
            return [
                'id' => $jugador->id,
                'nombre' => $jugador->nombre,
                'equipo' => $jugador->equipo->nombre,
            ];
        });

    return response()->json(['jugadores' => $jugadores]);
});

Route::middleware('auth:sanctum')->post('/save-fcm-token', [FcmTokenController::class, 'store']);

Route::post('/notificaciones/enviar', [FCMController::class, 'enviarNotificacion']);

Route::middleware('auth:sanctum')->post('/send-notification', [NotificationController::class, 'send']);

Route::post('/usuario/fcm-token', [UserController::class, 'guardarFcmToken']);
