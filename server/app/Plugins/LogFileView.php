<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Plugins;

use App\Plugins\Contracts\LogFileViewInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Paginator\Paginator;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Collection;

class LogFileView implements LogFileViewInterface
{
    /**
     * @var int
     */
    public $currentContentPage = 1;

    /**
     * @var int
     */
    public $currentFilePage = 1;

    /**
     * @var int
     */
    public $perPage = 10;

    /**
     * @var string
     */
    public $currentFileName = '';

    /**
     * @var string
     */
    public $currentLogGroup = '';

    public $keyword = '';

    public $level = '';

    public $param;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var mixed
     */
    private $pattern;

    /**
     * @var mixed
     */
    private $path;

    /**
     * @var float|int
     */
    private $maxSize = 50 * 1024 * 1024;

    /**
     * @var Collection
     */
    private $detail;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var array
     */
    private $class = [
        'debug'     => 'text-primary',
        'info'      => 'text-primary',
        'notice'    => 'text-secondary',
        'warning'   => 'text-warning',
        'error'     => 'text-danger',
        'critical'  => 'text-danger',
        'alert'     => 'text-danger',
        'emergency' => 'text-danger',
        'processed' => 'text-primary',
        'failed'    => 'text-danger',
    ];

    /**
     * @var array
     */
    private $logLevel = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
        'processed',
        'failed',
    ];

    /**
     * LogFile constructor.
     */
    public function __construct()
    {
        $this->config  = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $this->path    = $this->config->get('log_view.path');
        $this->pattern = $this->config->get('log_view.pattern');
    }

    /**
     * @return int
     */
    public function getLogListTotal(): int
    {
        return $this->collection->count();
    }

    /**
     * @return int
     */
    public function getDetailTotal(): int
    {
        return $this->detail->count();
    }

    /**
     * @return Collection|Paginator|string
     */
    public function getDetailForPage()
    {
        return $this->getDetail()->paginator($this->detail, $this->currentContentPage);
    }

    /**
     * @return Collection|Paginator|string
     */
    public function getLogListForPage()
    {
        return $this->getLogFile()->paginator($this->collection, $this->currentFilePage);
    }

    /**
     * @param Collection $collection
     * @param $filePage
     *
     * @return Paginator
     */
    private function paginator(Collection $collection, $filePage): Paginator
    {
        return new Paginator($collection, (int) $this->perPage, (int) $filePage);
    }

    /**
     * @return $this
     */
    private function getLogFile(): LogFileView
    {
        $dirs   = scandir($this->path);
        $logAll = [];
        foreach ($dirs as $dir) {
            if (! ($dir != '.' && $dir != '..')) {
                continue;
            }
            $filePattern = sprintf('%s%s/%s', $this->path, $dir, $this->pattern);
            if ($filePattern) {
                $collection = new Collection(glob($filePattern));
                if ($collection->isNotEmpty()) {
                    $collection = $collection->filter(function ($log) {
                        return filesize($log) < $this->maxSize;
                    })->map(function ($log) use ($dir) {
                        return [
                            'file_name' => $this->getFileName($log),
                            'log_group' => $dir,
                        ];
                    })->unique();
                    $log        = $collection->toArray();
                    $logAll     = array_merge($logAll, $log);
                }
            }
        }
        $this->collection = new Collection($logAll);
        return $this;
    }

    /**
     * @param string $logFile
     *
     * @return false|string
     */
    private function getFileName(string $logFile)
    {
        return substr($logFile, strrpos($logFile, '/') + 1);
    }

    /**
     * @return $this
     */
    private function getDetail(): LogFileView
    {
        $lineList = [];
        $fullPath = $this->config->get('log_view.path') . $this->currentLogGroup . DIRECTORY_SEPARATOR . $this->currentFileName;
        if (file_exists($fullPath)) {
            $content    = $this->readFileLine($fullPath);
            $collection = (new Collection($content));
            if ($collection->isNotEmpty()) {
                $collection->each(function ($content) use (&$lineList) {
                    $content = strtolower(trim($content));
                    foreach ($this->logLevel as $level) {
                        $match = $this->pregMatch($level, $content);
                        if (empty($match[4])) {
                            continue;
                        }
                        if ($this->keyword) {
                            preg_match("/{$this->keyword}/i", $match[4], $keyword);
                            if (empty($keyword)) {
                                continue;
                            }
                        }
                        if ($this->level && $this->level !== $level) {
                            continue;
                        }
                        $expSub     = explode('.', substr(preg_replace("/^\n*/", '', $content), 22));
                        $lineList[] = [
                            'context' => $expSub[0] ?? $match[3],
                            'level'   => $level,
                            'class'   => $this->class[$level],
                            'date'    => $match[1],
                            'text'    => str_replace(["\r", "\n"], '', $match[4]),
                            'in_file' => isset($current[5]) ? $match[5] : '',
                            'stack'   => preg_replace("/^\n*/", '', $content),
                        ];
                    }
                });
            }
        }
        $this->detail = (new Collection($lineList))->sortByDesc('date')->values();
        return $this;
    }

    /**
     * @param $level
     * @param $content
     *
     * @return mixed
     */
    private function pregMatch($level, $content)
    {
        preg_match(
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i',
            $content,
            $match
        );
        return $match;
    }

    /**
     * @param $fullPath
     *
     * @return array
     */
    private function readFileLine($fullPath): array
    {
        $content = [];
        $handle  = fopen($fullPath, 'rb+');
        if (is_resource($handle)) {
            while (feof($handle) == false) {
                $line = fgets($handle);
                if ($line) {
                    $content[] = $line;
                }
            }
        }
        return $content;
    }
}
