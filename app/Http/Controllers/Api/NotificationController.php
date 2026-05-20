<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    // POST /api/notifications/test
    // Envía una notificación al usuario autenticado
    public function test(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body'  => 'required|string|max:255',
        ]);

        $user = $request->user();
        $tokenCount = $user->fcmTokens()->count();

        if ($tokenCount === 0) {
            return response()->json([
                'message' => 'El usuario no tiene tokens FCM registrados.',
                'hint'    => 'Registra un token primero con POST /api/fcm-token',
            ], 422);
        }

        $this->notifications->sendToUser(
            $user,
            $request->title,
            $request->body,
            ['source' => 'test']
        );

        return response()->json([
            'message' => 'Notificación enviada',
            'user'    => $user->email,
            'tokens'  => $tokenCount,
        ]);
    }

    // POST /api/notifications/broadcast
    // Envía una notificación a TODOS los usuarios con token registrado
    public function broadcast(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body'  => 'required|string|max:255',
        ]);

        $totalTokens = \App\Models\FcmToken::count();

        if ($totalTokens === 0) {
            return response()->json([
                'message' => 'No hay tokens FCM registrados en el sistema.',
            ], 422);
        }

        $this->notifications->sendToAll(
            $request->title,
            $request->body,
            ['source' => 'broadcast']
        );

        return response()->json([
            'message' => 'Broadcast enviado',
            'tokens'  => $totalTokens,
        ]);
    }

    // POST /api/notifications/send-to-user
    // Envía una notificación a un usuario específico por email (para pruebas admin)
    public function sendToUser(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'title' => 'required|string|max:100',
            'body'  => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();
        $tokenCount = $user->fcmTokens()->count();

        if ($tokenCount === 0) {
            return response()->json([
                'message' => "El usuario {$request->email} no tiene tokens FCM registrados.",
            ], 422);
        }

        $this->notifications->sendToUser($user, $request->title, $request->body, ['source' => 'manual']);

        return response()->json([
            'message' => 'Notificación enviada',
            'user'    => $user->email,
            'tokens'  => $tokenCount,
        ]);
    }
}
