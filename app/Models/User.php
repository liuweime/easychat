<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/19
 * Time: 13:47
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $guarded = [];

    protected $fillable = ['name', 'password', 'email'];

    /**
     * 用户一对多房间
     * 房间一对多用户
     */
    public function chatRoom()
    {
        return $this->belongsToMany(ChatRoom::class, 'user_with_chat_rooms', 'uid', 'room_id');
    }
}
