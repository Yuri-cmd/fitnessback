<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required|string',
            'platform' => 'sometimes|string|in:ios,android',
        ]);

        $request->user()->fcmTokens()->updateOrCreate(
            ['token' => $request->token],
            ['platform' => $request->input('platform', 'ios')]
        );

        return response()->json(['message' => 'Token guardado']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        $request->user()->fcmTokens()
            ->where('token', $request->token)
            ->delete();

        return response()->json(['message' => 'Token eliminado']);
    }
}
