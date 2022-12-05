<?php

declare(strict_types=1);

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-template',
    description: 'It creates all required files for puzzle.'
)]
final class CreateTemplate extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $today = new \DateTimeImmutable();
        $year = $today->format('Y');
        $day = $today->format('d');

        $dir = sprintf(__DIR__ . '/../../%s/Day%s', $year, $day);

        $solutionTemplate = $this->getSolutionClassContent($year, $day);

        $solutionContent = strtr($solutionTemplate, ['{year}' => $year, '{day}' => $day]);

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

    private function getSolutionClassContent(string $year, string $day): string
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

        return strtr($solutionTemplate, ['{year}' => $year, '{day}' => $day]);
    }
}
