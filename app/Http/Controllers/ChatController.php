<?php

namespace App\Http\Controllers;

use App\Events\ChatMessage;
use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * ChatController
 * 
 * Handles chat message operations
 */
class ChatController extends Controller
{
    /**
     * Send a new chat message
     *
     * @param SendMessageRequest $request
     * @return JsonResponse
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        // dd($request);
        try {
            // Get validated data with defaults
            $data = $request->getValidatedData();

            // Create and save the message
            $message = Message::createMessage(
                content: $data['content'],
                channel: $data['channel'],
                userName: $data['user_name'],
                userId: $data['user_id']
            );

            // Broadcast the message
            broadcast(new ChatMessage($message));

            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'data' => $message->toBroadcastArray()
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'data' => $data ?? null
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }

    /**
     * Get messages for a channel
     *
     * @param string $channel
     * @return JsonResponse
     */
    public function getMessages(string $channel = 'general'): JsonResponse
    {
        try {
            $messages = Message::getChannelMessages($channel)
                ->map->toBroadcastArray();

            return response()->json([
                'status' => 'success',
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch messages', [
                'error' => $e->getMessage(),
                'channel' => $channel
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch messages. Please try again.'
            ], 500);
        }
    }
}
