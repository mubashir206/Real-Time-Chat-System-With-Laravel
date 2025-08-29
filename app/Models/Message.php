<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function seenByUsers()
    {
        return $this->belongsToMany(User::class, 'message_user_seen')->withTimestamps();
    }
}
