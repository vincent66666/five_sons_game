<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

/**
 * @property int $id
 * @property string $title 房间名称
 * @property int $create_uid 房间创建者
 * @property int $player1_id 玩家1
 * @property int $player1_ready 玩家1是否准备就绪 1是 2否
 * @property int $player2_id 玩家2
 * @property int $player2_ready 玩家2是否准备就绪 1是 2否
 * @property int $status 状态 1 开启 、2 关闭
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $watch_member_ids 观战用户列表
 * @property Member $create_user
 * @property string $status_text
 * @property Member $player1
 * @property Member $player2
 */
class Room extends Model implements CacheableInterface
{
    use Cacheable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'create_uid',
        'player1_id',
        'player1_ready',
        'player2_id',
        'player2_ready',
        'status',
        'created_at',
        'updated_at',
        'watch_member_ids',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'create_uid'       => 'integer',
        'player1_id'       => 'integer',
        'player1_ready'    => 'integer',
        'player2_id'       => 'integer',
        'player2_ready'    => 'integer',
        'status'           => 'integer',
        'watch_member_ids' => 'json',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function player1(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(Member::class, 'id', 'player1_id');
    }

    public function player2(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(Member::class, 'id', 'player2_id');
    }

    public function create_user(): \Hyperf\Database\Model\Relations\HasOne
    {
        return $this->hasOne(Member::class, 'id', 'create_uid');
    }

    public function getStatusTextAttribute($value): string
    {
        switch ($value) {
            case '1':
                $text = '等待开始';
                break;
            case '2':
            default:
                $text = '游戏中';
                break;
        }
        return $text;
    }
}
