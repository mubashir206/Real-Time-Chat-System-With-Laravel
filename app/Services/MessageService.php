<?php

namespace App\Services;

use App\Enum\GroupRole;
use App\Enum\GroupType;
use App\Events\GroupMessageSent;
use App\Events\GroupMessagesSeen;
use App\Events\GroupUserTyping;
use App\Helpers\ConversationHelper;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    // Send message to a conversation
    public function sendToConversation(Request $request)
    {
        $conversation = Conversation::findOrFail($request->input('conversation_id'));
        ConversationHelper::authorizeParticipant($conversation);
        $msg = Message::create([
            'user_id' => Auth::id(),
            'conversation_id' => $conversation->id,
            'group_id' => null,
            'message' => $request->input('message'),
            'is_seen' => false,
        ]);
        $conversation->touch();
        return response()->json(['success' => true, 'data' => $msg], 201);
    }

    // Get conversation messages + mark as seen
    public function getConversationMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        ConversationHelper::authorizeParticipant($conversation);
        $messages = Message::with('user:id,name')
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    // Send message to a group
    public function sendToGroup(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        // must be a member
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$member) {
            abort(403, 'You are not a member of this group.');
        }
        // if private group -> only admin can send
        if ($group->group_type === GroupType::PRIVATE && $member->role !== GroupRole::ADMIN) {
            abort(403, 'Only admin can send messages in a private group.');
        }
        $msg = Message::create([
            'user_id' => Auth::id(),
            'conversation_id' => null,
            'group_id' => $group->id,
            'message' => $request->input('message'),
            'is_seen' => false,
        ]);
        $group->touch();
        broadcast(new GroupMessageSent($msg))->toOthers();
        return response()->json(['success' => true, 'data' => $msg], 201);
    }

    // Get group messages (no per-user read receipts with current schema)
    public function getGroupMessages($groupId)
    {
        $group = Group::findOrFail($groupId);
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->exists();
        if (!$isMember) {
            abort(403, 'You are not a member of this group.');
        }
        $messages = Message::with('user:id,name')
            ->where('group_id', $group->id)
            ->orderBy('created_at', 'asc')
            ->get();

        //  mark as seen globally not per-user
        Message::where('group_id', $group->id)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_seen' => true]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function startTypingInGroup($groupId)
    {
        $group = Group::findOrFail($groupId);
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$member) {
            abort(403, 'You are not a member of this group.');
        }
        if ($group->group_type === GroupType::PRIVATE && $member->role !== GroupRole::ADMIN->value) {
            abort(403, 'Only admin can send messages in a private group.');
        }
        $user = Auth::user();
        broadcast(new GroupUserTyping($user->id, $groupId, $user->name, true))->toOthers();
        return response()->json(['success' => true]);
    }

    public function stopTypingInGroup($groupId)
    {
        $group = Group::findOrFail($groupId);
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$member) {
            abort(403, 'You are not a member of this group.');
        }
        if ($group->group_type === GroupType::PRIVATE && $member->role !== GroupRole::ADMIN->value) {
            abort(403, 'Only admin can send messages in a private group.');
        }
        $user = Auth::user();
        broadcast(new GroupUserTyping($user->id, $groupId, $user->name, false))->toOthers();
        return response()->json(['success' => true]);
    }

    public function markGroupMessagesAsSeen($groupId)
    {
        $group = Group::findOrFail($groupId);
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();
        if (!$member) {
            abort(403, 'You are not a member of this group.');
        }
        $messages = Message::where('group_id', $groupId)
            ->where('user_id', '!=', Auth::id())
            ->whereDoesntHave('seenByUsers', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();
        $messageIds = $messages->pluck('id')->toArray();
        $updatedCount = 0;
        foreach ($messages as $message) {
            $message->seenByUsers()->attach(Auth::id());
            $updatedCount++;
        }
        $groupMembers = GroupMember::where('group_id', $groupId)->pluck('user_id')->toArray();
        foreach ($messages as $message) {
            $seenByCount = $message->seenByUsers()->count();
            if ($seenByCount >= count($groupMembers) - 1) { // Exclude sender
                $message->update(['is_seen' => true]);
            }
        }
        if ($updatedCount > 0) {
            broadcast(new GroupMessagesSeen($groupId, Auth::id(), $messageIds))->toOthers();
        }
        return response()->json(['success' => true, 'updated_count' => $updatedCount]);
    }
}
