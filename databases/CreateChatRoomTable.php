<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/22
 * Time: 17:16
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateChatRoomTable extends Migration
{
    private $schema;
    private $table;

    public function __construct()
    {
        $this->schema = Capsule::schema();
        $this->table = 'chat_rooms';
    }

    public function up()
    {
        if ($this->schema->hasTable($this->table)) {
            return false;
        }

        $this->schema->create($this->table, function (Blueprint $table) {
            $table->increments('id')->comment('房间ID');
            $table->string('name', 100)->nullable(false)->default('')->comment('房间名');
            $table->integer('number')->nullable(false)->default(0)->comment('房间人数');
            $table->tinyInteger('type')->nullable(false)->default(0)->comment('房间类型');
            $table->string('img_path', 255)->nullable(false)->default('')->comment('房间封面地址');
            $table->string('thumb_path', 255)->nullable(false)->default('')->comment('房间封面缩略图地址');
            $table->tinyInteger('is_del')->nullable(false)->default(0)->comment('是否删除');

            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists($this->table);
    }
}
