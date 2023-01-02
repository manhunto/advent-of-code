<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\AnswersService;
use App\Services\FileSystem;
use App\Services\PuzzleMetadataFetcher;
use App\Services\SolutionFactory;
use App\Year;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fetch-puzzle-input-and-output',
    description: 'Fetches puzzle input and output from server'
)]
final class FetchPuzzleInputAndOutputCommand extends Command
{
    private const YEAR = 'year';

    public function __construct(
        private readonly AnswersService $answersService,
        private readonly FileSystem $fileSystem,
        private readonly SolutionFactory $solutionFactory,
        private readonly PuzzleMetadataFetcher $puzzleMetadataFetcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(self::YEAR,  InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $year = Year::fromString($input->getArgument(self::YEAR));

        $dates = $this->solutionFactory->getAllDatesForSolverInYear($year);

        if (empty($dates)) {
            $io->info('There is no any solution for year ' . $year . '. Skipped.');

            return Command::SUCCESS;
        }
        $pb = $io->createProgressBar(count($dates));

        foreach ($dates as $date) {
            $pb->advance(1);

            // Input
            $metadata = $this->puzzleMetadataFetcher->fetch($date);
            $this->fileSystem->createFile($date, 'puzzle.in', $metadata->puzzleInput);

            //  Result
            $aocResult = $this->answersService->fetchAnswers($date);
            $this->fileSystem->savePuzzleAnswers($date, $aocResult);
        }

        $pb->finish();
        $io->success('All puzzle inputs and outputs were fetched.');

        return Command::SUCCESS;
    }
}
