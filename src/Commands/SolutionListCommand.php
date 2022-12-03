<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\SolutionFactory;
use App\Services\SolutionsDescriptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:list',
    description: 'It lists all solutions'
)]
final class SolutionListCommand extends Command
{
    public function __construct(
        private readonly SolutionFactory $solutionFactory,
        private readonly SolutionsDescriptor $solutionsDescriptor,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];
        foreach ($this->solutionFactory->iterate() as $solution) {
            $description = $this->solutionsDescriptor->getDescription($solution);

            $rows[] = [
                $description->date->day,
                $description->date->year,
                $description->name ? sprintf('<href=%s>%s</>', $description->href, $description->name) : '---'
            ];
        }

        $style = new SymfonyStyle($input, $output);
        $style->table(
            ['Date', 'Year', 'Name'],
            $rows
        );

        return Command::SUCCESS;
    }
}
