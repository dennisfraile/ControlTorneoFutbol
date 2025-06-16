<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !hash::check($request->password, $user->password))
        {
            return response()->json(['message'=>'Credenciales invalidas'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token'=>$token,
            'user'=>$user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
