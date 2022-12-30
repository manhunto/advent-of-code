<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day19;

use App\Utils\Collection;
use App\Utils\Output\Console as C;

class FactoryChecker
{
    public function howMuchGeocodeCanProduce(Factory $factory, int $maxMinutes): int
    {
        $minute = 1;
        $factories = [$factory];

        do {
            $newFactories = [];
            foreach ($factories as $otherFactory) {
                $newFactories = [...$newFactories, ...$otherFactory->clone()];
            }

            var_dump(count($newFactories));

            $factories = $newFactories;
            $minute++;
            var_dump($minute);
        } while($minute < $maxMinutes);

        return Collection::create($factories)
            ->forEach(static fn (Factory $factory): int => $factory->getGeode())
            ->max();
    }
}
