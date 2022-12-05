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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list',
    description: 'It lists all solutions'
)]
final class SolutionListCommand extends Command
{
    private const RESOLVED_ICON = '✅';
    private const RESOLVED_INCORRECTLY_ICON = '❌';

    public function __construct(
        private readonly SolutionFactory $factory,
        private readonly SolutionsDescriptor $descriptor,
        private readonly SolutionRunner $runner,
    ) {
        parent::__construct();
    }

    /**
     * @throws FileNotFound
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];
        foreach ($this->factory->iterate() as $solution) {
            $description = $this->descriptor->getDescription($solution);

            $isExampleResolvedCorrectly = $this->runner->run($solution, InputType::Example)->isResolvedCorrectly();
            $isPuzzleResolvedCorrectly = $this->runner->run($solution, InputType::Puzzle)->isResolvedCorrectly();

            $rows[] = [
                $description->date->day,
                $description->date->year,
                $description->name ? sprintf('<href=%s>%s</>', $description->href, $description->name) : '---',
                new TableSeparator(),
                $this->renderIcon($isExampleResolvedCorrectly),
                $this->renderIcon($isPuzzleResolvedCorrectly),
            ];
        }

        $style = new SymfonyStyle($input, $output);
        $style->table(
            ['Day', 'Year', 'Name', new TableSeparator(), 'Example', 'Puzzle'],
            $rows
        );

        return Command::SUCCESS;
    }

    private function renderIcon(bool $isResolvedCorrectly): string
    {
        return $isResolvedCorrectly ? self::RESOLVED_ICON : self::RESOLVED_INCORRECTLY_ICON;
    }
}
