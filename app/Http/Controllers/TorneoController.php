<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;
use App\Models\Torneo;
use Kreait\Firebase\Factory;

class TorneoController extends Controller
{

    public function store(Request $request, FirebaseService $firebaseService)
    {
        $factory = (new Factory)->withServiceAccount(storage_path('firebase_credentials.json'));
        $messaging = $factory->createMessaging();

        $messaging->send([
            'token' => $request->user->device_token,
            'notification' => [
                'title' => 'Nuevo torneo',
                'body' => 'Se ha creado un nuevo torneo',
            ],
        ]);
    }
    public function limpiar()
    {
        DB::beginTransaction();
        try{
            \App\Models\Estadistica::truncate();
            \App\Models\Partido::truncate();
            \App\Models\Jugador::truncate();
            \App\Models\Equipo::truncate();

            DB::commit();
            return response()->json(['message' => 'Torneo limpiado correctamente']);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json(['error' => 'Error al limpiar torneo'], 500);
        }
    }
}
