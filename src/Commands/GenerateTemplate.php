<?php

declare(strict_types=1);

namespace App\Commands;

use App\Date;
use App\Services\DateFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-template',
    description: 'Generate all required files for puzzle.'
)]
final class GenerateTemplate extends Command
{
    public function __construct(
        private readonly DateFactory $dateFactory
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = $this->dateFactory->createForToday();

        $dir = sprintf(__DIR__ . '/../../%s/Day%s', $date->year, $date->day);

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
        if (!is_dir($path) && !mkdir($path) && !is_dir($path)) {
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
    name: '',
)]
final class Solution implements Solver
{
    public function solve(Input \$input): Result
    {
        return new Result(123);
    }
}
TXT;

        return strtr($solutionTemplate, ['{year}' => $date->year, '{day}' => $date->day]);
    }
}
