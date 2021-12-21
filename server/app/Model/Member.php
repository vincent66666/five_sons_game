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
use Qbhy\HyperfAuth\AuthAbility;

/**
 * @property int $id
 * @property string $username 登录账号
 * @property string $nickname 用户名
 * @property string $password 密码
 * @property string $last_login_ip 最后登录ip
 * @property string $last_login_time 最后登录时间
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $avatar 头像
 */
class Member extends Model implements \Qbhy\HyperfAuth\Authenticatable, CacheableInterface
{
    use AuthAbility;
    use Cacheable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'username', 'nickname', 'password', 'last_login_ip', 'last_login_time', 'created_at', 'updated_at', 'avatar'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getAvatarUrlAttribute($value): string
    {
        return fileUpload()->url($value);
    }
}
