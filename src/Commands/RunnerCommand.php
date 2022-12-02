<?php

declare(strict_types=1);

namespace App\Commands;

use App\Date;
use App\Exceptions;
use App\InputType;
use App\Result;
use App\Services\FileLoader;
use App\Services\SolutionFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'execute',
    description: 'It executes task for given day'
)]
final class RunnerCommand extends Command
{
    private const OPTION_DAY = 'day';
    private const OPTION_YEAR = 'year';
    private const OPTION_PUZZLE = 'puzzle';

    public function __construct(
        private readonly SolutionFactory $solutionFactory,
        private readonly FileLoader $fileLoader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(self::OPTION_YEAR, 'y', InputOption::VALUE_REQUIRED)
            ->addOption(self::OPTION_DAY, 'd', InputOption::VALUE_REQUIRED)
            ->addOption(self::OPTION_PUZZLE, 'p', InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $this->prepareDate($input);
        $inputType = $this->prepareInputType($input);

        $style = new SymfonyStyle($input, $output);

        try {
            $solution = $this->solutionFactory->create($date);
        } catch (\Exception $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            $inputFile = $this->fileLoader->loadInput($date, $inputType);
            $expectedResultFileContent = $this->fileLoader->loadExpectedOutput($date, $inputType);
        } catch (Exceptions\FileNotFound $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }

        $expectedResult = Result::fromArray($expectedResultFileContent->asArray());

        $result = $solution->solve($inputFile);
        $style->info((string) $result);

        if ($result->equals($expectedResult)) {
            $style->success('Result is OK');

            return Command::SUCCESS;
        }

        $style->error('Unexpected result. Expected was...');
        $style->error((string) $expectedResult);

        return Command::FAILURE;
    }

    private function prepareDate(InputInterface $input): Date
    {
        $date = Date::createForToday();

        if ($dayInput = $input->getOption(self::OPTION_DAY)) {
            $date = $date->withDay($dayInput);
        }

        if ($yearInput = $input->getOption(self::OPTION_YEAR)) {
            $date = $date->withYear($yearInput);
        }

        return $date;
    }

    private function prepareInputType(InputInterface $input): InputType
    {
        return $input->getOption(self::OPTION_PUZZLE) ? InputType::Puzzle : InputType::Example;
    }
}
