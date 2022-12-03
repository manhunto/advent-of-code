<?php

declare(strict_types=1);

$today = new DateTimeImmutable();
$year = $today->format('Y');
$day = $today->format('d');

$dir = sprintf(__DIR__ . '/../%s/Day%s', $year, $day);

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
    href: 'https://adventofcode.com/{year}/day/{day}'
)]
final class Solution implements Solver
{
    public function solve(Input \$input): Result
    {
        return new Result(123);
    }
}
TXT;

$solutionContent = strtr($solutionTemplate, ['{year}' => $year, '{day}' => $day]);

createDir($dir);
createFile($dir . '/example.in');
createFile($dir . '/example.out');
createFile($dir . '/puzzle.in');
createFile($dir . '/puzzle.out');
createFile($dir . '/Solution.php', $solutionContent);

function createDir(string $path): void
{
    if (!is_dir($path) && !mkdir($path) && !is_dir($path)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
    }
}

function createFile(string $fileName, ?string $content = null): void
{
    if (!file_exists($fileName)) {
        touch($fileName);

        if ($content) {
            file_put_contents($fileName, $content);
        }
    }
}
