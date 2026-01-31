<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $selectedUserId = $request->query('user_id'); // ¿Con quién estamos chateando?

        // 1. OBTENER LA LISTA DE CONVERSACIONES (Contactos recientes)
        // Buscamos mensajes donde soy sender o receiver, ordenamos por fecha
        $latestMessages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->with(['sender', 'receiver'])
            ->get();

        // Filtramos para obtener usuarios únicos (los "contactos")
        $conversations = $latestMessages->map(function ($message) use ($userId) {
            // Si yo soy el sender, el contacto es el receiver, y viceversa
            return $message->sender_id === $userId ? $message->receiver : $message->sender;
        })->unique('id')->values();

        // 2. OBTENER EL HISTORIAL DEL CHAT SELECCIONADO
        $messages = [];
        $selectedUser = null;

        if ($selectedUserId) {
            $selectedUser = User::findOrFail($selectedUserId);

            // Marcar como leídos los mensajes que recibí de este usuario
            Message::where('sender_id', $selectedUserId)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Traer la conversación (Mis envíos y Sus envíos)
            $messages = Message::where(function ($q) use ($userId, $selectedUserId) {
                $q->where('sender_id', $userId)->where('receiver_id', $selectedUserId);
            })->orWhere(function ($q) use ($userId, $selectedUserId) {
                $q->where('sender_id', $selectedUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc') // Importante: Orden cronológico para el chat
            ->get();
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'messages' => $messages,
            'selectedUser' => $selectedUser,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string|max:1000',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
        ]);

        // Redirigir de vuelta al chat con este usuario
        return to_route('messages.index', ['user_id' => $request->receiver_id]);
    }
}