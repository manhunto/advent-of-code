<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\AnswersService;
use App\Services\FileSystem;
use App\Services\PuzzleMetadataFetcher;
use App\Services\SolutionFactory;
use App\SolutionFile;
use App\Year;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fetch-puzzle-input-and-output',
    description: 'Fetches puzzle input and output from server'
)]
final class FetchPuzzleInputAndOutputCommand extends Command
{
    private const YEAR = 'year';
    private const FORCE = 'force';

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
        $this->addArgument(self::YEAR,  InputArgument::REQUIRED)
            ->addOption(self::FORCE, 'f', InputOption::VALUE_NONE, 'Force update files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $year = Year::fromString($input->getArgument(self::YEAR));

        $dates = $this->solutionFactory->getAllDatesForSolverInYear($year);

        if (empty($dates)) {
            $io->info('There is no any solution for year ' . $year . '. Skipped.');

            return Command::SUCCESS;
        }

        $pb = $io->createProgressBar(count($dates));
        $force = $input->getOption(self::FORCE);

        $updated = 0;

        foreach ($dates as $date) {
            $pb->advance();

            // Input
            if ($force || $this->fileSystem->hasPuzzleInput($date) === false) {
                $metadata = $this->puzzleMetadataFetcher->fetch($date);
                $this->fileSystem->createFile($date, SolutionFile::puzzleIn(), $metadata->puzzleInput);
                $updated++;
            }

            //  Result
            if ($force || $this->fileSystem->hasPuzzleOutput($date) === false) {
                $aocResult = $this->answersService->fetchAnswers($date);
                $this->fileSystem->savePuzzleAnswers($date, $aocResult);
                $updated++;
            }
        }

        $pb->finish();
        $io->newLine();
        $io->success($updated . ' files was updated.');

        return Command::SUCCESS;
    }
}
