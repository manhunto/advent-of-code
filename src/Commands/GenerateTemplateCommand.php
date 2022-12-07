<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Helpers\DateInputHelper;
use App\Date;
use App\Exceptions\ApiException;
use App\Exceptions\DateCannotBeGeneratedForToday;
use App\PuzzleMetadata;
use App\Services\PuzzleMetadataFetcher;
use App\SolverFullyQualifiedClassname;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-template',
    description: 'Generate all required files for puzzle.'
)]
final class GenerateTemplateCommand extends Command
{
    private const WITHOUT_FETCH = 'without-fetch';

    public function __construct(
        private readonly DateInputHelper $dateInputHelper,
        private readonly PuzzleMetadataFetcher $puzzleMetadataFetcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(DateInputHelper::OPTION_YEAR, 'y', InputOption::VALUE_REQUIRED)
            ->addOption(DateInputHelper::OPTION_DAY, 'd', InputOption::VALUE_REQUIRED)
            ->addOption(self::WITHOUT_FETCH, 'f', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        try {
            $date = $this->dateInputHelper->prepareDate($input);
        } catch (DateCannotBeGeneratedForToday) {
            $style->error('Today\'s date is out of advent of code so you have to provide --day or/and --year options.');

            return Command::FAILURE;
        }

        $puzzleMetadata = null;
        if (!$input->getOption(self::WITHOUT_FETCH)) {
            try {
                $puzzleMetadata = $this->puzzleMetadataFetcher->fetch($date);
            } catch (ApiException $e) {
                $style->error('There is some error occurred during fetching puzzle metadata. Use --' . self::WITHOUT_FETCH . ' option to skip fetching.');
                $style->error($e->getMessage());

                return Command::FAILURE;
            }
        }

        $dir = sprintf(__DIR__ . '/../../%s/Day%s', $date->getYearAsString(), $date->day);

        $solutionContent = $this->getSolutionClassContent($date, $puzzleMetadata);

        $this->createDir($dir);
        $this->createFile($dir . '/example.in');
        $this->createFile($dir . '/example.out');
        $this->createFile($dir . '/puzzle.in', $puzzleMetadata?->puzzleInput);
        $this->createFile($dir . '/puzzle.out');
        $this->createFile($dir . '/Solution.php', $solutionContent);

        $style = new SymfonyStyle($input, $output);
        $style->success('Files existed or were generated in ' . $dir);

        $fqn = SolverFullyQualifiedClassname::fromDate($date);

        if (!$fqn->classExists()) {
            $style->caution('It looks like you generated class in not configured namespace. Add namespace to composer.json and configure this namespace in config/services.php');
        }

        return self::SUCCESS;
    }

    private function createDir(string $path): void
    {
        if (!is_dir($path) && !mkdir($path, recursive: true) && !is_dir($path)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
        }
    }

    private function createFile(string $fileName, ?string $content = null): void
    {
        if (!file_exists($fileName)) {
            touch($fileName);

            if ($content) {
                file_put_contents($fileName, $content);
            }
        }
    }

    private function getSolutionClassContent(Date $date, ?PuzzleMetadata $puzzleMetadata): string
    {
        $solutionTemplate = <<<TXT
<?php

declare(strict_types=1);

namespace AdventOfCode{year}\Day{day};

use App\Input;
use App\Result;
use App\SolutionAttribute;
use App\Solver;

#[SolutionAttribute(
    name: '{puzzleName}',
)]
final class Solution implements Solver
{
    public function solve(Input \$input): Result
    {
        foreach (\$input->asArray() as \$row) {
        
        }
    
        return new Result(123);
    }
}
TXT;

        return strtr($solutionTemplate, [
            '{year}' => $date->getYearAsString(),
            '{day}' => $date->day,
            '{puzzleName}' => $puzzleMetadata?->puzzleName ?? "TODO Change me"
        ]);
    }
}
