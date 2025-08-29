<?php

namespace App\Models;

use App\Enum\GroupType;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'group_type' => GroupType::class,
    ];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members() {
        return $this->hasMany(GroupMember::class);
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

}
