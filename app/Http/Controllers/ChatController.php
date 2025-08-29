<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function personalChat(){
        return view('chat.personal');
    }

    public function groupChat(){
        return view('chat.group');
    }
}
