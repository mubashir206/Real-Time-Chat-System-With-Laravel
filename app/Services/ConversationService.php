<?php

namespace App\Services;

use App\Events\{MessageSent, MessagesSeen, UserTyping};
use App\Events\TestEvent;
use App\Helpers\ConversationHelper;
use App\Models\{Conversation, Message};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationService
{
    public function index()
    {
        $authId = Auth::id();
        $conversations = Conversation::with(['userOne:id,name', 'userTwo:id,name'])
            ->where('user_one_id', $authId)
            ->orWhere('user_two_id', $authId)
            ->latest('updated_at')
            ->get();
        return response()->json(['success' => true, 'data' => $conversations]);
    }

    public function findOrCreate(Request $request)
    {
        $authId = Auth::id();
        $otherId = (int) $request->input('other_user_id');

        // Check if conversation exists (A,B) or (B,A)
        $conversation = Conversation::where(function ($q) use ($authId, $otherId) {
            $q->where('user_one_id', $authId)->where('user_two_id', $otherId);
        })->orWhere(function ($q) use ($authId, $otherId) {
            $q->where('user_one_id', $otherId)->where('user_two_id', $authId);
        })->first();

        if (!$conversation) {
            // Normalize order to avoid duplicates
            $userOne = min($authId, $otherId);
            $userTwo = max($authId, $otherId);

            $conversation = Conversation::create([
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
            ]);
        }
        return response()->json(['success' => true, 'data' => $conversation], 201);
    }

    public function messages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        ConversationHelper::authorizeParticipant($conversation);
        $messages = Message::with('user:id,name')
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages (from other user) as seen
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_seen', false)
            ->update(['is_seen' => true]);
        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function send(Request $request)
    {
        $conversation = Conversation::findOrFail($request->input('conversation_id'));
        ConversationHelper::authorizeParticipant($conversation);
        $msg = Message::create([
            'user_id' => Auth::id(),
            'conversation_id' => $request->input('conversation_id'),
            'group_id' => null,
            'message' => $request->input('message'),
            'is_seen' => false,
        ]);
        // Touch conversation updated_at for ordering lists
        $conversation->touch();
        broadcast(new MessageSent($msg))->toOthers();
        return response()->json(['success' => true, 'data' => $msg], 201);
    }

    public function markSeen($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        ConversationHelper::authorizeParticipant($conversation);
        $updatedCount = Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_seen', false)
            ->update(['is_seen' => true]);
        if ($updatedCount > 0) {
            broadcast(new MessagesSeen($conversationId, Auth::id()))->toOthers();
        }
        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount
        ]);
    }

    public function startTyping(Request $request)
    {
        $conversation = Conversation::findOrFail($request->input('conversation_id'));
        ConversationHelper::authorizeParticipant($conversation);
        $user = Auth::user();
        broadcast(new UserTyping(
            $user->id,
            $request->input('conversation_id'),
            $user->name,
            true
        ))->toOthers();
        return response()->json(['success' => true]);
    }

    public function stopTyping(Request $request)
    {
        $conversation = Conversation::findOrFail($request->input('conversation_id'));
        ConversationHelper::authorizeParticipant($conversation);
        $user = Auth::user();
        broadcast(new UserTyping(
            $user->id,
            $request->input('conversation_id'),
            $user->name,
            false
        ))->toOthers();
        return response()->json(['success' => true]);
    }
}
