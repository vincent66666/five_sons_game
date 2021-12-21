<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 190)->unique()->comment('登录账号');
            $table->string('nickname', 190)->comment('用户名');
            $table->string('password', 255)->comment('密码');
            $table->string('last_login_ip')->nullable()->default('')->comment('最后登录ip');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
}
