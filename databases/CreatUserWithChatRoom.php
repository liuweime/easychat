<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/22
 * Time: 17:37
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreatUserWithChatRoom extends Migration
{
    private $schema;
    private $table;

    public function __construct()
    {
        $this->schema = Capsule::schema();
        $this->table = 'user_with_chat_rooms';
    }

    public function up()
    {
        if ($this->schema->hasTable($this->table)) {
            return false;
        }

        $this->schema->create($this->table, function (Blueprint $table) {
            $table->increments('id')->comment('关联ID');
            $table->integer('room_id')->nullable(false)->default(0)->comment('房间号');
            $table->integer('uid')->nullable(false)->default(0)->comment('用户ID');
            $table->index('uid', 'idx_ucr_uid');
            $table->index('room_id', 'idx_ucr_roomid');

            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists($this->table);
    }
}
