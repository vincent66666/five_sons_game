<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Service;

use App\Exception\ErrorResponseException;
use DateTime;

class FilesystemService extends Service
{
    public function upload($files, $upload_path = null)
    {
        if (isset($files['file'])) {
            $upFiles = $files['file'];
        } else {
            throw new ErrorResponseException(0, '上传文件不存在！');
        }
        if (is_array($upFiles)) {
            $fileList = [];
            foreach ($upFiles as $k => $v) {
                $instance   = fileUpload()->make($v, $upload_path);
                $fileList[] = $instance->upload();
            }
            return $fileList;
        }
        $instance = fileUpload()->make($upFiles, $upload_path);
        return $instance->upload();
    }

    public function url($path): string
    {
        return fileUpload()->url($path);
    }

    public function listUrl(array $pathList = [], $isPath = false): array
    {
        $list = [];
        foreach ($pathList as $path) {
            $url = fileUpload()->url($path);
            if ($isPath) {
                $list[] = [
                    'path' => $path,
                    'url'  => $url,
                ];
            } else {
                $list[] = $url;
            }
        }
        return $list;
    }

    public function aliOssPolicy(): array
    {
        $bucket   = config('file.storage.oss.bucket');
        $endpoint = config('file.storage.oss.endpoint');
        $id       = config('file.storage.oss.accessId');          // 请填写您的AccessKeyId。
        $key      = config('file.storage.oss.accessSecret');     // 请填写您的AccessKeySecret。
        // $host的格式为 bucket.name.endpoint，请替换为您的真实信息。
        $host = "https://{$bucket}.{$endpoint}";
        if (! empty(config('upload.oss_public_url'))) {
            $host = config('upload.oss_public_url');
        }
        // $callbackUrl为上传回调服务器的URL，请将下面的IP和Port配置为您自己的真实URL信息。
        $callbackUrl = 'https://limestone.jooau.com/lm-ipad/oss/callback';
        $dir         = config('upload.upload_file_directory') . '/';         // 用户上传文件时指定的前缀。

        $callback_param       = [
            'callbackUrl'      => $callbackUrl,
            'callbackBody'     => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => 'application/x-www-form-urlencoded',
        ];
        $callback_string      = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);

        $now        = time();
        $expire     = config('file.storage.oss.expire') ?? 300;  //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问。
        $end        = $now + $expire;
        $expiration = $this->gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition    = [0 => 'content-length-range', 1 => 0, 2 => 1048576000];
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start          = [0 => 'starts-with', 1 => '$key', 2 => $dir];
        $conditions[]   = $start;
        $arr            = ['expiration' => $expiration, 'conditions' => $conditions];
        $policy         = json_encode($arr);
        $base64_policy  = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature      = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        return [
            'dir'       => $dir,
            'expire'    => $end,
            'host'      => $host,
            'accessId'  => $id,
            'policy'    => $base64_policy,
            'signature' => $signature,
            'callback'  => $base64_callback_body,
        ];
    }

    public function gmt_iso8601($time): string
    {
        $dtStr        = date('c', $time);
        $my_date_time = new DateTime($dtStr);
        $expiration   = $my_date_time->format(DateTime::ISO8601);
        $pos          = strpos($expiration, '+');
        $expiration   = substr($expiration, 0, $pos);
        return $expiration . 'Z';
    }
}
