<?php

namespace App\Models;

use App\Enum\GroupRole;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'role' => GroupRole::class, 
    ];

    public function group() {
        return $this->belongsTo(Group::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
