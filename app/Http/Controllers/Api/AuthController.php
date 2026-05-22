<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8',
            'birth_date' => 'nullable|date|before:today',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->filled('birth_date')) {
            UserProfile::create([
                'user_id'    => $user->id,
                'birth_date' => $request->birth_date,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'name'  => $user->name,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'name'  => $user->name,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        Log::warning('deleteAccount called', [
            'user_id'    => $user->id,
            'email'      => $user->email,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        $user->tokens()->delete();
        $user->delete(); // soft delete — sets deleted_at, preserves all records
        return response()->json(['message' => 'Cuenta eliminada']);
    }
}
