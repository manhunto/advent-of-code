<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use App\Commands\RunnerCommand;
use App\Services\FileLoader;
use App\Services\SolutionFactory;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new RunnerCommand(
    new SolutionFactory(),
    new FileLoader()
));
$application->run();
