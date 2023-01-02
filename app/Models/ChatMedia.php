<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMedia extends Model
{
    use HasFactory;
    protected $table = "chat_media";
    protected $fillable = ['chat_id', 'name', 'type', 'is_delete'];
}
