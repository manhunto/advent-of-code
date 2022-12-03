<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class App extends Application
{
    public function __construct(iterable $commands, string $name, string $version)
    {
        $commands = $commands instanceof Traversable ? iterator_to_array($commands) : $commands;

        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct($name, $version);
    }
}

$container = new ContainerBuilder();
$loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/config'));
$loader->load('services.php');
$container->compile();
$container->get(App::class)->run();
