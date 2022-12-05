<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\FileNotFound;
use App\InputType;
use App\Services\SolutionFactory;
use App\Services\SolutionRunner;
use App\Services\SolutionsDescriptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list',
    description: 'List with all solutions'
)]
final class SolutionListCommand extends Command
{
    private const RESOLVED_ICON = '✅';
    private const RESOLVED_INCORRECTLY_ICON = '❌';

    private const OPTION_YEAR = 'year';

    public function __construct(
        private readonly SolutionFactory $factory,
        private readonly SolutionsDescriptor $descriptor,
        private readonly SolutionRunner $runner,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(self::OPTION_YEAR, 'y', InputOption::VALUE_REQUIRED, 'List puzzles for given year. Default year: current year');
    }

    /**
     * @throws FileNotFound
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $availableYears = $this->factory->getAvailableYears();
        $year = $this->getYear($input);

        if (!in_array($year, $availableYears, true)) {
            $style->error('There is no puzzles for given year. Use --year option to list puzzles for different year. Given: ' . $year);
            $style->info('Available years: ' . implode(', ', $availableYears));

            return Command::FAILURE;
        }

        $rows = [];
        foreach ($this->factory->iterateForYear($year) as $solution) {
            $description = $this->descriptor->getDescription($solution);

            $isExampleResolvedCorrectly = $this->runner->run($solution, InputType::Example)->isResolvedCorrectly();
            $isPuzzleResolvedCorrectly = $this->runner->run($solution, InputType::Puzzle)->isResolvedCorrectly();

            $rows[] = [
                $description->date->day,
                $description->name ? sprintf('<href=%s>%s</>', $description->href, $description->name) : '---',
                new TableSeparator(),
                $this->renderIcon($isExampleResolvedCorrectly),
                $this->renderIcon($isPuzzleResolvedCorrectly),
            ];
        }

        $style->createTable()
            ->setHeaderTitle($year)
            ->setHeaders(['Day', 'Name', new TableSeparator(), 'Example', 'Puzzle'])
            ->setRows($rows)
            ->render();

        return Command::SUCCESS;
    }

    private function renderIcon(bool $isResolvedCorrectly): string
    {
        return $isResolvedCorrectly ? self::RESOLVED_ICON : self::RESOLVED_INCORRECTLY_ICON;
    }

    private function getYear(InputInterface $input): string
    {
        if ($year = $input->getOption(self::OPTION_YEAR)) {
            return (string) $year;
        }

        return (new \DateTimeImmutable())->format('Y');
    }
}
