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
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\HttpServer\Router\RouteCollector;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Command
 */
class ShowRouterCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        parent::__construct('show:routes');
        $this->container = $container;
        $this->config    = $config;
        $this->setDescription('查看路由');
    }

    public function handle()
    {
        $path    = $this->input->getOption('path');
        $server  = $this->input->getOption('server');
        $factory = $this->container->get(DispatcherFactory::class);
        $router  = $factory->getRouter($server);
        $this->show(
            $this->analyzeRouter($server, $router, $path),
            $this->output
        );
    }

    protected function configure()
    {
        $this->setDescription('Describe the routes information.')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Get the detail of the specified route information by path')
            ->addOption('server', 'S', InputOption::VALUE_OPTIONAL, 'Which server you want to describe routes.', 'http');
    }

    protected function analyzeRouter(string $server, RouteCollector $router, ?string $path)
    {
        $data                              = [];
        [$staticRouters, $variableRouters] = $router->getData();

        foreach ($staticRouters as $method => $items) {
            foreach ($items as $handler) {
                $this->analyzeHandler($data, $server, $method, $path, $handler);
            }
        }
        foreach ($variableRouters as $method => $items) {
            foreach ($items as $item) {
                if (is_array($item['routeMap'] ?? false)) {
                    foreach ($item['routeMap'] as $routeMap) {
                        $this->analyzeHandler($data, $server, $method, $path, $routeMap[0]);
                    }
                }
            }
        }
        return $data;
    }

    protected function analyzeHandler(array &$data, string $serverName, string $method, ?string $path, Handler $handler)
    {
        $uri = $handler->route;
        if (! is_null($path) && ! Str::contains($uri, $path)) {
            return;
        }
        if (is_array($handler->callback)) {
            $action = $handler->callback[0] . '::' . $handler->callback[1];
        } elseif (is_string($handler->callback)) {
            $action = $handler->callback;
        } elseif (is_callable($handler->callback)) {
            $action = 'Closure';
        } else {
            $action = (string) $handler->callback;
        }
        $unique = "{$serverName}|{$uri}";
        if (isset($data[$unique])) {
            $data[$unique]['method'][] = $method;
        } else {
            // method,uri,name,action,middleware
            $registeredMiddlewares = MiddlewareManager::get($serverName, $uri, $method);
            $middlewares           = $this->config->get('middlewares.' . $serverName, []);

            $middlewares   = array_merge($middlewares, $registeredMiddlewares);
            $data[$unique] = [
                'server'      => $serverName,
                'method'      => [$method],
                'uri'         => $uri,
                'action'      => $action,
                'middleware'  => implode(PHP_EOL, array_unique($middlewares)),
                'router_name' => $handler->options['router_name'],
                'parent_name' => $handler->options['parent_name'] ?? '',
            ];
        }
    }

    private function show(array $data, OutputInterface $output)
    {
        $rows = [];
        foreach ($data as $route) {
            $route['method'] = implode('|', $route['method']);
            $rows[]          = $route;
            $rows[]          = new TableSeparator();
        }
        $rows  = array_slice($rows, 0, count($rows) - 1);
        $table = new Table($output);
        $table
            ->setHeaders(['Server', 'Method', 'URI', 'Action', 'Middleware', 'RouterName', 'parentName'])
            ->setRows($rows);
        $table->render();
    }
}
