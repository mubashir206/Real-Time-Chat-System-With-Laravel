<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddMemberRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdatePrivacyRequest;
use App\Services\GroupService;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function store(StoreGroupRequest $request)
    {
        return $this->groupService->store($request);
    }

    public function myGroups()
    {
        return $this->groupService->myGroups();
    }

    public function addMember(AddMemberRequest $request, $groupId)
    {
        return $this->groupService->addMember($request, $groupId);
    }

    public function removeMember($groupId, $userId)
    {
        return $this->groupService->removeMember($groupId, $userId);
    }

    public function updatePrivacy(UpdatePrivacyRequest $request, $groupId)
    {
        return $this->groupService->updatePrivacy($request, $groupId);
    }

    public function getMembers($groupId)
    {
        return $this->groupService->getMembers($groupId);
    }
}
