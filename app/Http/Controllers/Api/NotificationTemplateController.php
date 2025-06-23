<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationTempalte;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;
class NotificationTemplateController extends Controller
{
    public function index()
    {
        return NotificationTempalte::all();
    }

    public function store(Request $request)
    {
        $template = NotificationTempalte::create($request->only('title', 'body'));
        return response()->json($template, 201);
    }

    public function sendFromTemplate(Request $request, $id)
    {
        $template = NotificationTempalte::findOrFail($id);

        // Reutiliza lógica de envío por rol...


        NotificationLog::create([
            'title' => $template->title,
            'body' => $template->body,
            'role_sent_to' => $request->role,
            'user_id' => Auth::id()
        ]);

        return response()->json(['message' => 'Notificación enviada.']);
    }
    public function logs()
    {
        return NotificationLog::with('user')->latest()->get();
    }
}
