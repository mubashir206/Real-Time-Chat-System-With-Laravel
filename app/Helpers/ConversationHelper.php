<?php

namespace App\Helpers;

use App\Enum\GroupRole;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;

class ConversationHelper
{

    public static function authorizeParticipant(Conversation $conversation): void
    {
        $authId = Auth::id();
        if (!in_array($authId, [$conversation->user_one_id, $conversation->user_two_id])) {
            abort(403, 'You are not part of this conversation.');
        }
    }

    public static function ensureAdmin(Group $group): void
    {
        $isAdmin = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('role', GroupRole::ADMIN->value)
            ->exists();

        if (!$isAdmin) {
            abort(403, 'Only group admin can perform this action.');
        }
    }
}
