<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use \App\Models\User;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            Http::withHeaders([
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $user->fcm_token,
                'notification' => [
                    'title' => $request->title,
                    'body' => $request->body,
                ],
            ]);
        }

        return response()->json(['message' => 'Notificaciones enviadas']);
    }

    public function senByRole(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'role' => 'required|string|in:admin,colaborador,representante',
        ]);

        $users = User::where('role', $request->role)
                    ->whereNotNull('fcm_token')
                    ->get();

        foreach ($users as $user) {
            Http::withHeaders([
                'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $user->fcm_token,
                'notification' => [
                    'title' => $request->title,
                    'body' => $request->body,
                ],
            ]);
        }
        return response()->json(['message' => 'Notificaciones enviadas a usuarios con rol ' . $request->role]);
    }
}
