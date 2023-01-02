<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table = "chats";
    protected $fillable = ['user_id', 'receiver_id', 'message_text', 'type', 'is_deleted', 'deleted_by', 'tick'];

    public function receiver()
    {
        return $this->hasOne(User::class, 'id', 'receiver_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
