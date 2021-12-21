<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 190)->unique()->comment('房间名称');
            $table->bigInteger('create_uid')->default(0)->comment('房间创建者');
            $table->bigInteger('player1_id')->default(0)->comment('玩家1');
            $table->tinyInteger('player1_ready')->default(0)->comment('玩家1是否准备就绪 1是 2否');
            $table->bigInteger('player2_id')->default(0)->comment('玩家2');
            $table->tinyInteger('player2_ready')->default(0)->comment('玩家2是否准备就绪 1是 2否');
            $table->tinyInteger('status')->default(1)->comment('状态 1 开启 、2 关闭');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
}
