<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Devtool\Generator\GeneratorCommand;
use Hyperf\Utils\CodeGen\Project;

/**
 * @Command
 */
class MakeServiceCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('make:service');
        $this->setDescription('创建服务类');
    }

    protected function getPath($name): string
    {
        $project = new Project();
        return BASE_PATH . '/' . $project->path($name . 'Service');
    }

    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/service.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Service';
    }
}
