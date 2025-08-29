<?php

namespace App\Http\Controllers;

use App\Enum\GroupRole;
use App\Enum\GroupType;
use App\Events\GroupMessagesSeen;
use App\Events\GroupUserTyping;
use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\SendToGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    // Send message to a conversation
    public function sendToConversation(SendMessageRequest $request)
    {
        return $this->messageService->sendToConversation($request);
    }

    // Get conversation messages + mark as seen
    public function getConversationMessages($conversationId)
    {
        return $this->messageService->getConversationMessages($conversationId);
    }

    // Send message to a group
    public function sendToGroup(SendToGroupRequest $request, $groupId)
    {
        return $this->messageService->sendToGroup($request, $groupId);
    }

    public function getGroupMessages($groupId)
    {
        return $this->messageService->getGroupMessages($groupId);
    }

    public function startTypingInGroup($groupId)
    {
        return $this->messageService->startTypingInGroup($groupId);
    }

    public function stopTypingInGroup($groupId)
    {
        return $this->messageService->stopTypingInGroup($groupId);
    }

    public function markGroupMessagesAsSeen($groupId)
    {
        return $this->messageService->markGroupMessagesAsSeen($groupId);
    }

}
