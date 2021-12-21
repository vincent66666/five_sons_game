<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Plugins\Contracts;

use App\Plugins\FileUpload;

interface FileUploadInterface
{
    public function make($file, $upload_path = null): FileUpload;

    public function url($path);

    public function upload();
}
