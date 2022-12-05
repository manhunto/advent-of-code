<?php

declare(strict_types=1);

namespace App\Commands;

use App\Commands\Helpers\DateInputHelper;
use App\Date;
use App\Exceptions\DateCannotBeGeneratedForToday;
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
    public function __construct(
        private readonly DateInputHelper $dateInputHelper,
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
        $style = new SymfonyStyle($input, $output);

        try {
            $date = $this->dateInputHelper->prepareDate($input);
        } catch (DateCannotBeGeneratedForToday) {
            $style->error('Today\'s date is out of advent of code so you have to provide --day or/and --year options.');

            return Command::FAILURE;
        }

        $dir = sprintf(__DIR__ . '/../../%s/Day%s', $date->getYearAsString(), $date->day);

        $solutionContent = $this->getSolutionClassContent($date);

        $this->createDir($dir);
        $this->createFile($dir . '/example.in');
        $this->createFile($dir . '/example.out');
        $this->createFile($dir . '/puzzle.in');
        $this->createFile($dir . '/puzzle.out');
        $this->createFile($dir . '/Solution.php', $solutionContent);

        $style = new SymfonyStyle($input, $output);
        $style->success('Files existed or were generated in ' . $dir);

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

    private function getSolutionClassContent(Date $date): string
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
    name: 'TODO Change me',
)]
final class Solution implements Solver
{
    public function solve(Input \$input): Result
    {
        return new Result(123);
    }
}
TXT;

        return strtr($solutionTemplate, ['{year}' => $date->getYearAsString(), '{day}' => $date->day]);
    }
}
