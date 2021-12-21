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
class MakeRepositoryCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('make:repo');
        $this->setDescription('Create a new Repository-repositories class');
    }

    protected function getPath($name): string
    {
        $project = new Project();
        return BASE_PATH . '/' . $project->path($name . 'Repository');
    }

    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/repository/repositories.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Repository\\Repositories';
    }
}
