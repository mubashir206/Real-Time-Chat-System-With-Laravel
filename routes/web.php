<?php

use App\Events\TestEvent;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Register & Login
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Forget & Reset Password
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/add', [UserController::class, 'add'])->name('users.add');
    Route::get('/show-user', [UserController::class, 'showUser'])->name('users.show.user');
    Route::get('/user/status/{userId}', [UserController::class, 'getUserStatus'])->name('user.status');

    Route::get('/personal-chat', [ChatController::class, 'personalChat'])->name('personal.chat');
    Route::get('/group-chat', [ChatController::class, 'groupChat'])->name('group.chat');
    // Conversations
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations/find-or-create', [ConversationController::class, 'findOrCreate']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/messages/send', [ConversationController::class, 'send']);
    Route::post('/conversations/{conversationId}/mark-seen', [ConversationController::class, 'markSeen']);
    Route::post('/conversations/typing/start', [ConversationController::class, 'startTyping']);
    Route::post('/conversations/typing/stop', [ConversationController::class, 'stopTyping']);

    // Groups
    Route::post('/groups', [GroupController::class, 'store']);
    Route::get('/my-groups', [GroupController::class, 'myGroups']);
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember']);
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember']);
    Route::patch('/groups/{group}/privacy', [GroupController::class, 'updatePrivacy']);
    Route::get('/groups/{group}/members', [GroupController::class, 'getMembers']);

    // Group messages
    Route::get('/groups/{group}/messages', [MessageController::class, 'getGroupMessages']);
    Route::post('/groups/{group}/messages', [MessageController::class, 'sendToGroup']);
    Route::post('/groups/{group}/typing/start', [MessageController::class, 'startTypingInGroup']);
    Route::post('/groups/{group}/typing/stop', [MessageController::class, 'stopTypingInGroup']);
    Route::post('/groups/{group}/mark-seen', [MessageController::class, 'markGroupMessagesAsSeen']);

    // Conversation messages (endpoints)
    Route::get('/conversations/{conversation}/messages2', [MessageController::class, 'getConversationMessages']);
    Route::post('/conversations/{conversation}/messages2', [MessageController::class, 'sendToConversation']);
    // setting routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});


// Route::get('/pusher', function () {
//     return view('pusher');
// });
