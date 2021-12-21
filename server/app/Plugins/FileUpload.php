<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Plugins;

use App\Exception\ErrorResponseException;
use App\Plugins\Contracts\FileUploadInterface;

class FileUpload implements FileUploadInterface
{
    private $file;     //上传对象

    private $config;   //配置信息

    private $uploadPath; //相对路径

    private $stateMap = [ //上传状态
        'SUCCESS', //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        '文件大小超出 upload_max_filesize 限制',
        '文件大小超出 MAX_FILE_SIZE 限制',
        '文件未被完整上传',
        '没有文件被上传',
        '上传文件为空',
        'ERROR_TMP_FILE'           => '临时文件错误',
        'ERROR_TMP_FILE_NOT_FOUND' => '找不到临时文件',
        'ERROR_SIZE_EXCEED'        => '文件大小超出网站限制',
        'ERROR_TYPE_NOT_ALLOWED'   => '文件类型不允许',
        'ERROR_CREATE_DIR'         => '目录创建失败',
        'ERROR_DIR_NOT_WRITEABLE'  => '目录没有写权限',
        'ERROR_FILE_MOVE'          => '文件保存时出错',
        'ERROR_FILE_NOT_FOUND'     => '找不到上传文件',
        'ERROR_WRITE_CONTENT'      => '写入文件内容错误',
        'ERROR_UNKNOWN'            => '未知错误',
        'ERROR_DEAD_LINK'          => '链接不可用',
        'ERROR_HTTP_LINK'          => '链接不是http链接',
        'ERROR_HTTP_CONTENT_TYPE'  => '链接contentType不正确',
    ];

    /**
     * @var mixed
     */
    private $upload_adapter;

    public function make($file, $upload_path = null): FileUpload
    {
        $this->file           = $file;
        $this->config         = config('upload');
        $this->uploadPath     = $upload_path                    ?? $this->getUploadPath();
        $this->upload_adapter = $this->config['upload_adapter'] ?? 'local_public';
        return $this;
    }

    public function url($path): string
    {
        $path           = urldecode($path);
        $upload_adapter = config('upload.upload_adapter');
        switch ($upload_adapter) {
            case 'oss':
                if (! empty(config('upload.oss_public_url'))) {
                    $url = config('upload.oss_public_url') . DIRECTORY_SEPARATOR . $path;
                } else {
                    $bucket   = config('file.storage.oss.bucket');
                    $endpoint = empty(config('file.storage.oss.endpoint')) ? 'oss-cn-hangzhou.aliyuncs.com' : config('file.storage.oss.endpoint');
                    $url      = "https://{$bucket}.{$endpoint}/{$path}";
                }
                break;
            case 'cos':
            case 'qiniu':
                $url = filesystemAdapter($upload_adapter)->getUrl($path);
                break;
            case 'local_public':
            case 'local':
            default:
                $local_public_url = config('upload.local_public_url');
                $url              = $local_public_url . DIRECTORY_SEPARATOR . $path;
                break;
        }
        return $url;
    }

    public function upload(): string
    {
        // 校验上传的合法性
        if (! $this->file) {
            $stateInfo = $this->getStateInfo('ERROR_FILE_NOT_FOUND');
            throw new ErrorResponseException(0, $stateInfo);
        }
        // 获取上传文件信息
        $arr = $this->file->toArray();
        if ($arr['error']) {
            $stateInfo = $this->getStateInfo($arr['error']);
            throw new ErrorResponseException(0, $stateInfo);
        }
        if (! file_exists($arr['tmp_file'])) {
            $stateInfo = $this->getStateInfo('ERROR_TMP_FILE_NOT_FOUND');
            throw new ErrorResponseException(0, $stateInfo);
        }
        if (! is_uploaded_file($arr['tmp_file'])) {
            $stateInfo = $this->getStateInfo('ERROR_TMP_FILE');
            throw new ErrorResponseException(0, $stateInfo);
        }
        //检查文件大小是否超出限制
        if (! $this->checkSize($arr['size'])) {
            $stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');
            throw new ErrorResponseException(0, $stateInfo);
        }
        $ext = $this->file->getExtension();
        //检查是否不允许的文件格式
        if (! $this->checkType($ext)) {
            $stateInfo = $this->getStateInfo('ERROR_TYPE_NOT_ALLOWED');
            throw new ErrorResponseException(0, $stateInfo);
        }

        try {
            $imagePath = sprintf(
                '%s%s',
                $this->uploadPath,
                $this->getFilename($ext)
            );
            $stream    = fopen($this->file->getRealPath(), 'r+');
            filesystem($this->upload_adapter)->writeStream(
                $imagePath,
                $stream
            );
            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            throw new ErrorResponseException(0, $e->getMessage());
        }
        return $imagePath;
    }

    /**
     * getUploadPath
     * 获取文件上传相对目录
     * User：YM
     * Date：2020/2/28
     * Time：上午12:54.
     *
     * @return string
     */
    private function getUploadPath(): string
    {
        $attachments = trim($this->config['upload_file_directory'], DIRECTORY_SEPARATOR);
        $timePath    = date('Ymd');
        return $attachments . DIRECTORY_SEPARATOR . $timePath . DIRECTORY_SEPARATOR;
    }

    /**
     * getFilename
     * 生成文件名（存储重命名）
     * User：YM
     * Date：2020/2/27
     * Time：下午11:45.
     *
     * @param $fileType
     *
     * @return string
     */
    private function getFilename($fileType): string
    {
        //替换日期事件
        $t      = date('YmdHis');
        $format = $this->config['file_name_format'];
        $format = str_replace('{time}', $t, $format);
        //替换随机字符串
        $randNum = random_int(1, 10000000000) . random_int(1, 10000000000);
        if (preg_match('/\\{rand\\:([\\d]*)\\}/i', $format, $matches)) {
            $format = preg_replace('/\\{rand\\:[\\d]*\\}/i', substr($randNum, 0, (int) $matches[1]), $format);
        }
        return $format . '.' . $fileType;
    }

    /**
     * getStateInfo
     * 获取状态信息
     * User：YM
     * Date：2020/2/27
     * Time：下午11:25.
     *
     * @param $key
     *
     * @return string
     */
    private function getStateInfo($key): string
    {
        return empty($this->stateMap[$key]) ? $this->stateMap['ERROR_UNKNOWN'] : $this->stateMap[$key];
    }

    /**
     * checkType
     * 文件类型检测
     * User：YM
     * Date：2020/2/27
     * Time：下午11:07.
     *
     * @param $fileType
     *
     * @return bool
     */
    private function checkType($fileType): bool
    {
        return in_array(strtolower($fileType), $this->config['file_allow_files'], false);
    }

    /**
     * checkSize
     * 文件大小检测
     * User：YM
     * Date：2020/2/27
     * Time：下午11:08.
     *
     * @param $fileSize
     *
     * @return bool
     */
    private function checkSize($fileSize): bool
    {
        return $fileSize <= ($this->config['file_max_size']);
    }
}
