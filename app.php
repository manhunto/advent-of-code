<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\SolveCommand;
use App\Services\FileLoader;
use App\Services\SolutionFactory;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new SolveCommand(
    new SolutionFactory(),
    new FileLoader()
));
$application->run();
