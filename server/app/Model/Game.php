<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Model;

/**
 * @property int $id
 * @property int $room_id 房间ID
 * @property int $player1_color 玩家1棋子颜色
 * @property int $player2_color 玩家2棋子颜色
 * @property int $size 棋盘尺寸
 * @property string $map 棋盘
 * @property int $current_piece 当前出子颜色
 * @property int $last_go_x 最后落子的坐标X
 * @property int $last_go_y 最后落子的坐标Y
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Game extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'room_id',
        'player1_color',
        'player2_color',
        'size',
        'map',
        'current_piece',
        'last_go_x',
        'last_go_y',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'integer',
        'room_id'       => 'integer',
        'player1_color' => 'integer',
        'player2_color' => 'integer',
        'size'          => 'integer',
        'map'           => 'json',
        'current_piece' => 'integer',
        'last_go_x'     => 'integer',
        'last_go_y'     => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
