<?php

declare(strict_types=1);

namespace App\Commands;

use App\Date;
use App\Exceptions;
use App\Exceptions\ClassNotFound;
use App\Input;
use App\InputType;
use App\Result;
use App\Services\FileLoader;
use App\Services\SolutionFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'solve',
    description: 'It solves puzzle for given day. Default day: today'
)]
final class SolveCommand extends Command
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
            ->addOption(self::OPTION_PUZZLE, 'p', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $this->prepareDate($input);
        $inputType = $this->prepareInputType($input);

        $style = new SymfonyStyle($input, $output);

        try {
            $solution = $this->solutionFactory->create($date);
        } catch (ClassNotFound $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }

        try {
            $inputFileContent = $this->fileLoader->loadInput($date, $inputType);
            $expectedResultFileContent = $this->fileLoader->loadExpectedOutput($date, $inputType);
        } catch (Exceptions\FileNotFound $e) {
            $style->error($e->getMessage());

            return Command::FAILURE;
        }

        $expectedResult = Result::fromArray($expectedResultFileContent);
        $inputFile = Input::fromArray($inputFileContent);

        $result = $solution->solve($inputFile);

        if (!$result->equals($expectedResult)) {
            $style->error('Unexpected result.');
            $this->renderExpectedResult($style, $result, $expectedResult);

            return Command::FAILURE;
        }

        $style->success((string) $result);

        return Command::SUCCESS;
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

    private function renderExpectedResult(SymfonyStyle $style, Result $result, Result $expectedResult): void
    {
        $row = ['My result', $result->partOne, $result->partTwo ?: '---'];
        $secondRow = ['Expected result', $expectedResult->partOne, $expectedResult->partTwo ?: '---'];

        $style->createTable()
            ->addRows([$row, new TableSeparator(), $secondRow])
            ->setHeaders(['', 'Part one', 'Part two'])
            ->render();
    }
}
