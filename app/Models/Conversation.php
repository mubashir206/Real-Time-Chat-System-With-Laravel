<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = ['id'];

    public function userOne() {
        return $this->belongsTo(User::class, 'user_one_id');
    }
    
    public function userTwo() {
        return $this->belongsTo(User::class, 'user_two_id');
    }
    
    public function messages() {
        return $this->hasMany(Message::class);
    }
    
}
