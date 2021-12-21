<?php

declare(strict_types=1);
/**
 * @Created by PhpStorm
 * @User    : 清风醉
 */
namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Filesystem\Adapter\LocalAdapterFactory;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class TestCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('demo:test');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Test Command');
    }

    public function handle()
    {
        $this->line('Hello Hyperf!', 'info');
        var_dump(fileUpload()->url('Aff2.png'));
        $options = $this->container->get(ConfigInterface::class)->get('file', [
            'default' => 'local',
            'storage' => [
                'local' => [
                    'driver' => LocalAdapterFactory::class,
                    'root'   => BASE_PATH . '/runtime',
                ],
            ],
        ]);
        $url = container()->get(\Hyperf\Filesystem\FilesystemFactory::class)->getAdapter($options, 'cos')->getUrl('Aff2.png');
        var_dump($url);
    }
}
