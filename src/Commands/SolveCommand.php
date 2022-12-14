<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Helpers\DateInputHelper;
use App\Exceptions;
use App\Exceptions\ClassNotFound;
use App\Exceptions\FileNotFound;
use App\InputType;
use App\SolverResult;
use App\Services\SolutionFactory;
use App\Services\SolutionRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:solve',
    description: 'Solves puzzle for given day. Default day: today'
)]
final class SolveCommand extends Command
{
    private const OPTION_PUZZLE = 'puzzle';
    private const OPTION_EXAMPLE = 'example';

    public function __construct(
        private readonly SolutionFactory $factory,
        private readonly SolutionRunner $runner,
        private readonly DateInputHelper $dateInputHelper,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(DateInputHelper::OPTION_YEAR, 'y', InputOption::VALUE_REQUIRED)
            ->addOption(DateInputHelper::OPTION_DAY, 'd', InputOption::VALUE_REQUIRED)
            ->addOption(self::OPTION_PUZZLE, 'p', InputOption::VALUE_NONE)
            ->addOption(self::OPTION_EXAMPLE, 'e', InputOption::VALUE_REQUIRED, 'Solve puzzle for given example number. Available [2-5]');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        try {
            $date = $this->dateInputHelper->prepareDate($input);
        } catch (Exceptions\DateCannotBeGeneratedForToday) {
            $style->error('Today\'s date is out of advent of code so you have to provide --day or/and --year options.');

            return Command::FAILURE;
        }

        $inputType = $this->prepareInputType($input);


        try {
            $solution = $this->factory->create($date);
            $resultPair = $this->runner->run($solution, $inputType);
        } catch (ClassNotFound|FileNotFound $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }
        $style->block($inputType->name, 'Input type');
        $style->block($resultPair->getExecutionTimeInMiliSeconds(), 'Benchmark', style: 'info');

        if (!$resultPair->isResolvedCorrectly()) {
            $style->error('Unexpected result.');
            $this->renderSolverResult($style, $resultPair);

            return Command::FAILURE;
        }

        $style->success((string) $resultPair->getCurrentResult());

        return Command::SUCCESS;
    }

    private function prepareInputType(InputInterface $input): InputType
    {
        if ($input->getOption(self::OPTION_PUZZLE)) {
            return InputType::Puzzle;
        }

        $exampleNumber = $input->getOption(self::OPTION_EXAMPLE);

        if (is_numeric($exampleNumber) && $exampleNumber > 1) {
            return InputType::tryForExampleNumber((int) $exampleNumber);
        }

        return InputType::Example;
    }

    private function renderSolverResult(SymfonyStyle $style, SolverResult $resultPair): void
    {
        $currentResult = $resultPair->getCurrentResult();
        $expectedResult = $resultPair->getExpectedResult();
        $row = ['My result', $currentResult->partOne, $currentResult->partTwo ?: '---'];
        $secondRow = ['Expected result', $expectedResult->partOne ?: '---', $expectedResult->partTwo ?: '---'];

        $style->createTable()
            ->addRows([$row, new TableSeparator(), $secondRow])
            ->setHeaders(['', 'Part one', 'Part two'])
            ->render();
    }
}
