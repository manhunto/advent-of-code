<?php

declare(strict_types=1);

use App\Services\SolutionFactory;
use App\Solver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()      // Automatically injects dependencies in your services.
        ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->instanceof(Command::class)
        ->tag('app.command');

    $services->instanceof(Solver::class)
        ->tag('app.solver');

    $services->load('App\\', '../src/*')
        ->load('AdventOfCode2022\\', '../2022/');

    $services->set(App::class)
        ->class(App::class)
        ->public()
        ->args([tagged_iterator('app.command'), 'Advent of code', '1.0']);

    $services->set(SolutionFactory::class)
        ->class(SolutionFactory::class)
        ->args([tagged_iterator('app.solver')]);
};
