<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Helpers\DateInputHelper;
use App\Date;
use App\Exceptions\ApiException;
use App\InputType;
use App\Result;
use App\Services\AnswersService;
use App\Services\FileSystem;
use App\Services\SolutionFactory;
use App\Services\SolutionRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:upload',
    description: 'Uploads answers to AOC server'
)]
class UploadCommand extends Command
{
    public function __construct(
        private readonly AnswersService $answerService,
        private readonly SolutionFactory $factory,
        private readonly SolutionRunner $runner,
        private readonly DateInputHelper $dateInputHelper,
        private readonly FileSystem $fileSystem,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(DateInputHelper::OPTION_YEAR, 'y', InputOption::VALUE_REQUIRED)
            ->addOption(DateInputHelper::OPTION_DAY, 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $date = $this->dateInputHelper->prepareDate($input);

        try {
            $aocResult = $this->answerService->fetchAnswers($date);
        } catch (ApiException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->block((string) $aocResult, 'FETCHED ANSWERS');

        try {
            $solver = $this->factory->create($date);

            $myResult = $this->runner->run($solver, InputType::Puzzle)->getCurrentResult();
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        if ($myResult->equals($aocResult)) {
            $io->success('Did nothing. Answers are correct and have been uploaded already.');

            return Command::SUCCESS;
        }

        if ($this->isPartOneNotUploadedButSolved($aocResult, $myResult)) {
            $io->note('Uploading first part');

            return $this->upload($date, $myResult, 1, $io);
        }

        if ($aocResult->isPartOneSolved() && $this->isPartTwoNotUploadedButResolved($aocResult, $myResult)) {
            $io->note('Uploading second part');

            return $this->upload($date, $myResult, 2, $io);
        }

        return Command::SUCCESS;
    }

    private function isPartOneNotUploadedButSolved(Result $aocResult, Result $myResult): bool
    {
        return $aocResult->partOne === null && $myResult->isPartOneSolved();
    }

    private function isPartTwoNotUploadedButResolved(Result $aocResult, Result $myResult): bool
    {
        return $aocResult->partTwo === null && $myResult->isPartTwoSolved();
    }

    private function upload(Date $date, Result $myResult, int $level, SymfonyStyle $io): int
    {
        $answer = $myResult->getAnswerForLevel($level);

        try {
            $result = $this->answerService->uploadAnswer($date, $answer, $level);

            if (!$result) {
                $io->error('That is not correct answer. Answer: ' . $answer . ' for level ' . $level);

                return Command::FAILURE;
            }

            $this->fileSystem->savePuzzleAnswers($date, $myResult);

            $io->success('Answer for level ' . $level . ' has been uploaded and is correct. Answer saved to file.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
