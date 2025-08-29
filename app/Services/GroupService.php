<?php

namespace App\Services;

use App\Enum\GroupRole;
use App\Enum\GroupType;
use App\Events\GroupPrivacyChanged;
use App\Helpers\ConversationHelper;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupService
{
    public function store(Request $request)
    {
        $group = Group::create([
            'name' => $request->input('name'),
            'group_type' => $request->input('group_type'),
            'created_by' => Auth::id(),
        ]);
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => GroupRole::ADMIN->value,
        ]);
        return response()->json(['success' => true, 'data' => $group], 201);
    }

    public function myGroups()
    {
        $groups = Group::with(['members.user:id,name', 'creator:id,name'])
            ->whereHas('members', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get();
        return response()->json(['success' => true, 'data' => $groups]);
    }

    public function addMember(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        ConversationHelper::ensureAdmin($group);
        $exists = GroupMember::where('group_id', $group->id)
            ->where('user_id', $request->input('user_id'))
            ->exists();
        if ($exists) {
            return response()->json(['success' => true, 'message' => 'Already a member']);
        }
        $member = GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $request->input('user_id'),
            'role' => GroupRole::MEMBER->value,
        ]);
        return response()->json(['success' => true, 'data' => $member], 201);
    }

    public function removeMember($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);
        ConversationHelper::ensureAdmin($group);
        $target = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->firstOrFail();
        if ($target->role === GroupRole::ADMIN->value) {
            $adminCount = GroupMember::where('group_id', $group->id)
                ->where('role', GroupRole::ADMIN->value)
                ->count();

            if ($adminCount <= 1) {
                return response()->json(['error' => 'Cannot remove the only admin'], 422);
            }
        }
        $target->delete();
        return response()->json(['success' => true, 'message' => 'Member removed']);
    }

    public function updatePrivacy(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);
        ConversationHelper::ensureAdmin($group);
        $oldType = $group->group_type;
        $newType = $request->input('group_type');
        $group->update(['group_type' => $newType]);
        broadcast(new GroupPrivacyChanged($group, $oldType, $newType))->toOthers();
        return response()->json(['success' => true, 'data' => $group]);
    }

    public function getMembers($groupId)
    {
        $group = Group::findOrFail($groupId);
        $member = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$member) {
            abort(403, 'You are not a member of this group.');
        }
        $members = GroupMember::where('group_id', $groupId)
            ->with('user:id,name')
            ->get()
            ->map(function ($member) {
                return [
                    'user_id' => $member->user_id,
                    'name' => $member->user->name,
                    'role' => $member->role,
                ];
            });
        return response()->json(['success' => true, 'data' => $members]);
    }
}
