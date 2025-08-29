<?php

namespace App\Http\Controllers;

use App\Http\Requests\FindOrCreateConversationRequest;
use App\Http\Requests\SendMessageRequest;
use App\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    protected $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public function index()
    {
        return $this->conversationService->index();
    }

    public function findOrCreate(FindOrCreateConversationRequest $request)
    {
        return $this->conversationService->findOrCreate($request);
    }

    public function messages($conversationId)
    {
        return $this->conversationService->messages($conversationId);
    }

    public function send(SendMessageRequest $request)
    {
        return $this->conversationService->send($request);
    }

    public function markSeen($conversationId)
    {
        return $this->conversationService->markSeen($conversationId);
    }

    public function startTyping(Request $request)
    {
        return $this->conversationService->startTyping($request);
    }
    public function stopTyping(Request $request)
    {
        return $this->conversationService->stopTyping($request);
    }
}
