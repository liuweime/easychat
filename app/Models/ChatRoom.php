<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/22
 * Time: 17:33
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $table = 'chat_rooms';

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_with_chat_rooms', 'room_id', 'uid');
    }
}
