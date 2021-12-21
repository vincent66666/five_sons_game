<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('room_id')->default(0)->comment('房间ID');
            $table->integer('player1_color')->default(0)->comment('玩家1棋子颜色');
            $table->integer('player2_color')->default(0)->comment('玩家2棋子颜色');
            $table->integer('size')->default(0)->comment('棋盘尺寸');
            $table->json('map')->nullable()->comment('棋盘');
            $table->integer('current_piece')->default(0)->comment('当前出子颜色');
            $table->integer('last_go_x')->default(0)->comment('最后落子的坐标X');
            $table->integer('last_go_y')->default(0)->comment('最后落子的坐标Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
}
