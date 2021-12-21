<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
use Carbon\Carbon;
use Hyperf\Utils\Arr;

if (! function_exists('reloadRoute')) {
    /**
     * 加载路由.
     */
    function reloadRoute()
    {
        $path = BASE_PATH . '/routes';
        $dirs = scandir($path);
        foreach ($dirs as $dir) {
            if ($dir != '.' && $dir != '..') {
                $routeFilePath = $path . "/{$dir}";
                require_once $routeFilePath;
            }
        }
    }
}

if (! function_exists('getModuleName')) {
    /**
     * getModuleName
     * 获取所属模块.
     *
     * @param $classname
     * @param int $start
     *
     * @return string
     */
    function getModuleName($classname, int $start = 15): string
    {
        $name  = substr($classname, $start);
        $space = explode('\\', $name);
        if (count($space) > 1) {
            return $space[0];
        }
        return '';
    }
}

if (! function_exists('rmb2num')) {
    function rmb2num($str)
    {
        $chs = [1 => '壹', 2 => '贰', 3 => '叁', 4 => '肆', 5 => '伍', 6 => '陆', 7 => '柒', 8 => '捌', 9 => '玖'];
        $uni = [10 => '拾', 100 => '佰', 1000 => '仟', '0.1' => '角', '0.01' => '分'];

        if (mb_strpos($str, '亿')) {
            $arr                    = explode('亿', $str);
            $data['hundredMillion'] =  $arr[0];
            $str                    = $arr[1];
        }

        if (mb_strpos($str, '万')) {
            $arr                 = explode('万', $str);
            $data['tenThousand'] =  $arr[0];
            $str                 = $arr[1];
        }

        if (mb_strpos($str, '元')) {
            $arr          = explode('元', $str);
            $data['yuan'] =  $arr[0];
            $str          = $arr[1];
        } elseif (mb_strpos($str, '圆')) {
            $arr          = explode('圆', $str);
            $data['yuan'] =  $arr[0];
            $str          = $arr[1];
        }

        $data['fen'] = $str;
        $totalMoney  = 0;

        foreach ($data as $key => $value) {
            $money   = 0;
            $num     = [];
            $times   = [];
            $strarr  = [];
            $handStr = $value;

            for ($i = 0; $i < iconv_strlen($handStr); ++$i) {
                $strarr[] = mb_substr($handStr, $i, 1, 'utf8');
            }
            foreach ($strarr as $ke => $va) {
                if (in_array($va, $chs)) {
                    $num[] = array_search($va, $chs);
                }

                if (in_array($va, $uni)) {
                    $times[] = array_search($va, $uni);
                }
            }

            foreach ($num as $k => $v) {
                $t  = empty($times[$k]) ? 1 : $times[$k];
                $ls = $v * $t;
                $money += $ls;
            }

            if ($key == 'hundredMillion') {
                $money *= 100000000;
            } elseif ($key == 'tenThousand') {
                $money *= 10000;
            }
            // 元和分已经乘上他的加权了
            $totalMoney += $money;
        }

        return $totalMoney;
    }
}

if (! function_exists('getServerLocalIp')) {
    /**
     * getServerLocalIp
     * 获取服务端内网ip地址
     */
    function getServerLocalIp(): string
    {
        $ip  = '127.0.0.1';
        $ips = array_values(swoole_get_local_ip());
        foreach ($ips as $v) {
            if ($v && $v != $ip) {
                $ip = $v;
                break;
            }
        }

        return $ip;
    }
}

if (! function_exists('getClientIp')) {
    function getClientIp(): string
    {
        $ip_address = server_request()->getHeaderLine('x-forwarded-for');
        if (verify_ip($ip_address)) {
            return $ip_address;
        }
        $ip_address = server_request()->getHeaderLine('remote-host');
        if (verify_ip($ip_address)) {
            return $ip_address;
        }
        $ip_address = server_request()->getHeaderLine('x-real-ip');
        if (verify_ip($ip_address)) {
            return $ip_address;
        }
        $ip_address = server_request()->getServerParams()['remote_addr'] ?? '0.0.0.0';
        if (verify_ip($ip_address)) {
            return $ip_address;
        }
        return '0.0.0.0';
    }
}

if (! function_exists('verify_ip')) {
    function verify_ip($real_ip)
    {
        return filter_var($real_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}

if (! function_exists('uuid')) {
    function uuid($length)
    {
        if (function_exists('random_bytes')) {
            $uuid = bin2hex(random_bytes($length));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $random = openssl_random_pseudo_bytes($length, $isSourceStrong);
            if ($isSourceStrong === false || $random === false) {
                throw new \App\Exception\ErrorResponseException(500, 'IV generation failed');
            }
            $uuid = bin2hex($random);
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $uuid = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        }
        return $uuid;
    }
}

if (! function_exists('isMobileNum')) {
    /**
     * isMobileNum
     * 判断是否为手机号.
     *
     * @param $v
     *
     * @return bool
     */
    function isMobileNum($v): bool
    {
        $search = '/^(0|86|17951)?(13[0-9]|15[012356789]|166|17[3678]|18[0-9]|14[57])[0-9]{8}$/';
        if (preg_match($search, $v)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('isEmailNum')) {
    /**
     * isEmailNum
     * 判断是否为邮箱.
     *
     * @param $v
     *
     * @return bool
     */
    function isEmailNum($v): bool
    {
        $search = '/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/';
        if (preg_match($search, $v)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('isIdentityNum')) {
    /**
     * isIdentityNum
     * 判断是否为身份证号.
     *
     * @param $v
     *
     * @return bool
     */
    function isIdentityNum($v): bool
    {
        $search = '/^(^[1-9]\\d{7}((0\\d)|(1[0-2]))(([0|1|2]\\d)|3[0-1])\\d{3}$)|(^[1-9]\\d{5}[1-9]\\d{3}((0\\d)|(1[0-2]))(([0|1|2]\\d)|3[0-1])((\\d{4})|\\d{3}[Xx])$)$/';
        if (preg_match($search, $v)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('isPostalNum')) {
    /**
     * isPostalNum
     * 判断是否为邮编.
     *
     * @param $v
     *
     * @return bool
     */
    function isPostalNum($v): bool
    {
        $search = '/^[1-9]\\d{5}(?!\\d)$/';
        if (preg_match($search, $v)) {
            return true;
        }

        return false;
    }
}

if (! function_exists('encryptPassword')) {
    /**
     * encryptPassword
     * 加密密码
     *
     * @param string $password 用户输入的密码
     *
     * @return false|string
     */
    function encryptPassword(string $password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (! function_exists('checkPassword')) {
    /**
     * checkPassword
     * 检测密码
     *
     * @param $value
     * @param $hashedValue
     *
     * @return bool
     */
    function checkPassword($value, $hashedValue): bool
    {
        if ($hashedValue == '') {
            return false;
        }

        return password_verify($value, $hashedValue);
    }
}

if (! function_exists('generate_tree')) {
    function generate_tree(
        array $array,
        $pid_key = 'parent_id',
        $id_key = 'id',
        $children_key = 'children',
        $callback = null
    ): array {
        if (! $array) {
            return [];
        }
        //第一步 构造数据
        $items = [];
        foreach ($array as $value) {
            if ($callback && is_callable($callback)) {
                $callback($value);
            }
            $items[$value[$id_key]] = $value;
        }
        //第二部 遍历数据 生成树状结构
        $tree = [];
        foreach ($items as $key => $value) {
            //如果pid这个节点存在
            if (isset($items[$value[$pid_key]])) {
                $items[$value[$pid_key]][$children_key][] = &$items[$key];
            } else {
                $tree[] = &$items[$key];
            }
        }

        return $tree;
    }
}

if (! function_exists('generate_checkbox_tree')) {
    function generate_checkbox_tree(
        array $array,
        array $checked_arr = [],
        $pid_key = 'pid',
        $id_key = 'id',
        $label_key = 'label'
    ): array {
        $parents = [];
        //第一步 构造数据
        $items = [];
        foreach ($array as $value) {
            $items[$value[$id_key]] = [
                'label' => $value[$label_key],
                'value' => $value[$id_key],
            ];
            if ($value[$pid_key] > 0) {
                $parents[$value[$id_key]] = $value[$pid_key];
            } else {
                $items[$value[$id_key]]['checkAll']        = false;
                $items[$value[$id_key]]['isIndeterminate'] = false;
                $items[$value[$id_key]]['checkList']       = in_array($value[$id_key], $checked_arr, false)
                    ? [$value[$id_key]] : [];
            }
        }
        //第二部 遍历数据 生成树状结构
        $tree = [];
        foreach ($items as $key => $value) {
            $pid = $parents[$value['value']] ?? 0;
            //如果pid这个节点存在
            if (isset($items[$pid])) {
                $items[$pid]['options'][] = &$items[$key];
                if (in_array($key, $checked_arr, false)) {
                    $items[$pid]['checkList'][]     = $key;
                    $items[$pid]['isIndeterminate'] = true;
                    if (count($items[$pid]['checkList']) - 1 == count($items[$pid]['options'])) {
                        $items[$pid]['checkAll']        = true;
                        $items[$pid]['isIndeterminate'] = false;
                    }
                }
            } else {
                $tree[] = &$items[$key];
            }
        }

        return $tree;
    }
}

if (! function_exists('hideStr')) {
    /**
     * 数据脱敏.
     *
     * @param string $string 需要脱敏值
     * @param int $first_length 保留前n位
     * @param int $last_length 保留后n位
     * @param string $re 脱敏替代符号
     *
     * @return bool|string
     *                     例子:
     *                     hideStr('18811113683', 3, 4); //188****3683
     *                     hideStr('王富贵', 0, 1); //**贵
     */
    function hideStr(string $string, int $first_length = 0, int $last_length = 0, string $re = '*')
    {
        if (empty($string) || $first_length < 0 || $last_length < 0) {
            return $string;
        }
        $str_length = mb_strlen($string, 'utf-8');
        $first_str  = mb_substr($string, 0, $first_length, 'utf-8');
        $last_str   = mb_substr($string, -$last_length, $last_length, 'utf-8');
        if ($str_length <= 2 && $first_length > 0) {
            $replace_length = $str_length - $first_length;

            return $first_str . str_repeat($re, $replace_length > 0 ? $replace_length : 0);
        }

        if ($str_length <= 2 && $first_length == 0) {
            $replace_length = $str_length - $last_length;

            return str_repeat($re, $replace_length > 0 ? $replace_length : 0) . $last_str;
        }

        if ($str_length > 2) {
            $replace_length = $str_length - $first_length - $last_length;

            return $first_str . str_repeat('*', $replace_length > 0 ? $replace_length : 0)
                . $last_str;
        }
        if (empty($string)) {
            return $string;
        }
        return false;
    }
}

if (! function_exists('toArrayWalkSet')) {
    function toArrayWalkSet(&$array, $item)
    {
        array_walk($array, static function (&$value, $key, $arr) {
            $value = array_merge($value, $arr);
        }, $item);
    }
}

if (! function_exists('toKeyBy')) {
    function toKeyBy($arr, $keyField, $valueField = null): array
    {
        $ret = [];
        if ($valueField) {
            foreach ($arr as $row) {
                $ret[$row[$keyField]] = $row[$valueField];
            }
        } else {
            foreach ($arr as $row) {
                $ret[$row[$keyField]] = $row;
            }
        }
        return $ret;
    }
}

if (! function_exists('groupBy')) {
    function groupBy($arr, $keyField, $valueField = null): array
    {
        $ret = [];
        if ($valueField) {
            foreach ($arr as $row) {
                $ret[$row[$keyField]][] = $row[$valueField];
            }
        } else {
            foreach ($arr as $row) {
                $ret[$row[$keyField]][] = $row;
            }
        }
        return $ret;
    }
}

/*
 * 返回两个时间相差天数
 * 2019-11-9  2019-11-7
 */
if (! function_exists('count_days')) {
    function count_days($now_time, $normal_time): float
    {
        $now_time    = strtotime($now_time);
        $normal_time = strtotime($normal_time);
        $a_dt        = getdate($now_time);
        $b_dt        = getdate($normal_time);
        $a_new       = mktime(12, 0, 0, $a_dt['mon'], $a_dt['mday'], $a_dt['year']);
        $b_new       = mktime(12, 0, 0, $b_dt['mon'], $b_dt['mday'], $b_dt['year']);
        return round(($a_new - $b_new) / 86400);
    }
}

/*
 * 格式化前端返回日期
 */
if (! function_exists('carbon_string')) {
    function carbon_string($value): string
    {
        return Carbon::createFromTimestamp($value)->toDateTimeString();
    }
}
/*
 * 格式化前端返回日期
 */
if (! function_exists('carbon_timestamp')) {
    function carbon_timestamp($value)
    {
        return Carbon::parse($value)->timestamp;
    }
}

if (! function_exists('getUserUniqueId')) {
    /**
     * getUserUniqueId
     * 获取用户唯一标示，用户ID生成规则，32位.
     *
     * @param string $prefix
     *
     * @return string
     */
    function getUserUniqueId(string $prefix = 'qingfengzui'): string
    {
        // 前缀3位
        $prefix = substr($prefix, 0, 3);
        //随机字符串14位
        $rand = substr(str_replace(['/', '+', '='], '', base64_encode(random_bytes(14))), 0, 14);
        //根据当前时间生成的随机字符串11位
        $uniqid = substr(uniqid('', true), 2);
        //当前服务器ip后4位
        $ip     = getServerLocalIp();
        $ipList = explode('.', $ip);
        if (empty($ipList) || count($ipList) < 4) {
            $ipStr = '01';
        } else {
            $ipStr = $ipList[2] . $ipList[3];
        }
        $ip = dechex($ipStr);
        $ip = str_pad($ip, 6, 'f', STR_PAD_LEFT);
        if (PHP_SAPI != 'cli') {
            $ip = substr($ip, -4);
        } else {
            $ip = 'z' . substr($ip, -3);
        }

        //总共32位字符串
        return strtolower($prefix . $ip . $rand . $uniqid);
    }
}

if (! function_exists('oldDataDispose')) {
    /**
     * 跟剧新数据和老数量来处理出要新增的数据要删除的数据和更新的数据.
     *
     * @param $old
     * @param $new
     * @param string $key
     * @param string $primaryKey
     *
     * @return array
     */
    function oldDataDispose($old, $new, string $key = 'id', string $primaryKey = 'id'): array
    {
        $insert    = [];
        $update    = [];
        foreach ($new as $index => $item) {
            if (isset($old[$item[$key]])) {
                $item[$primaryKey] = $old[$item[$key]][$primaryKey];
                $update[]          = $item;
                unset($old[$item[$key]]);
            } else {
                $insert[] = $item;
            }
        }
        $delete = [];
        if (! empty($old)) {
            $delete = Arr::pluck($old, $primaryKey);
        }
        return compact('insert', 'update', 'old', 'delete');
    }
}

if (! function_exists('oldArrDispose')) {
    /**
     * 跟剧新数据和老数量来处理出要新增的数据要删除的数据和更新的数据.
     *
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    function oldArrDispose(array $old = [], array $new = []): array
    {
        //共同的部分
        $update = array_intersect($old, $new);

        //老的数据
        $old = array_diff($old, $update);

        //新的数据
        $add = array_diff($new, $update);

        return compact('add', 'old');
    }
}

if (! function_exists('generate_no')) {
    /**
     * 生成单号
     * <br />特点：不重复
     * <br />示例：
     * <br />普通付款：array('shop_id'=>1,'product_id'=>array(1,2,3), 'user_id'=>1, 'ip'=>'127.0.0.1', 'amount'=>0.01, 'timestamp'=>'2017-06-22 18:02:33', 'sign_key'=>'signkey!@#123_') 结果为: ib1bd7s9bc50c787114b195e7
     * <br />合并付款：generate_trade_no(array('shop_id'=>1,'product_id'=>array(1,2,3), 'user_id'=>1, 'ip'=>'127.0.0.1', 'amount'=>0.01, 'timestamp'=>'2017-06-22 18:02:33', 'sign_key'=>'signkey!@#123_')) 结果为：ib1bd7rs5c50c787114b195e7.
     *
     * @param array $data
     * @param null $time
     * @param bool $zipTime
     *
     * @return string 返回30或25位位字符串,格式为: 时间{年月日时分秒,14位}+md5{16位}=30位, 例如: 20170622180940c50c787114b195e7 或 ib1bd7s9bc50c787114b195e7
     */
    function generate_no(array $data, $time = null, bool $zipTime = true): string
    {
        ksort($data); // 根据数组的键值对数组重新排序
        $hashText = md5(json_encode($data)); // 把数组格式化为JSON字符串并生成MD5签名
        $time     = $time ?? date('YmdHis');
        if ($zipTime) {
            $time = base_convert($time, 10, 32); // 使用32进制将14位时间戳压缩到9位32进制值
        }
        // 把MD5签名截取16位, 并在签名加上14位或9位日期时间戳, 组成30位或25位字符串
        return $time . substr($hashText, 8, 16);
    }
}

if (! function_exists('apportionPrice')) {
    /**
     * 分摊优惠.
     *
     * @param $total
     * @param $num
     *
     * @return array
     */
    function apportionPrice($total, $num): array
    {
        $money_arr  = [];
        $safe_total = 0;
        $aa         = bcdiv($total, $num, 2);
        for ($i = 1; $i <= $num; ++$i) {
            if ($i == $num) {
                $money = bcsub($total, $safe_total, 2);
            } else {
                $money = $aa;
            }
            $safe_total  = bcadd($safe_total, $money, 2);
            $money_arr[] = $money;
        }
        return $money_arr;
    }
}
