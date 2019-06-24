<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/11/22
 * Time: 16:57
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration
{
    private $schema;
    private $table;

    public function __construct()
    {
        $this->schema = Capsule::schema();
        $this->table = 'users';
    }

    public function up()
    {
        if ($this->schema->hasTable($this->table)) {
            return false;
        }

        // 用户
        $this->schema->create($this->table, function (Blueprint $table) {
            $table->increments('id')->comment('用户ID');
            $table->string('name' , 20)->unique('username')->nullable(false)->default('')->comment('昵称');
            $table->string('password' , 255)->nullable(false)->default('')->comment('密码');
            $table->string('email' , 50)->unique('email')->nullable(false)->default('')->comment('邮箱');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->schema->dropIfExists($this->table);
    }
}
