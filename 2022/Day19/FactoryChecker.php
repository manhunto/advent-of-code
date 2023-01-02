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

        while ($minute <= $maxMinutes) {
            $newFactories = [];
            $maxGeode = 0;
            $theSameRobots = [];
            foreach ($factories as $otherFactory) {
                foreach ($otherFactory->clone() as $newFactory) {
                    $newFactories[] = $newFactory;
                    $theSameRobots[$newFactory->getRobotsHash()][] = $newFactory;
                    $maxGeode = max($maxGeode, $newFactory->getGeode());
                }
            }

            $newFactories = array_unique($newFactories);

            /**
             * @var int $i
             * @var Factory $newFactory
             */
            foreach ($newFactories as $i => $newFactory) {
                if ($newFactory->getGeode() < $maxGeode - 3) {
                    unset($newFactories[$i]);
                    continue;
                }

                $withTheSameRobots = array_filter(
                    $theSameRobots[$newFactory->getRobotsHash()],
                    static fn(Factory $A) => $A->isTheSame($newFactory) === false
                        && $A->hasBetterInventory($newFactory)
                );

                if (empty($withTheSameRobots) === false) {
                    unset($newFactories[$i]);
                }
            }

            $factories = $newFactories;

//            C::writeln();
//            C::writeln('Minute: '. $minute);
            C::writeln('Factories: '. count($factories));
//            C::writeln('Max geode: '. $maxGeode);
            $minute++;
        }

        return Collection::create($factories)
            ->forEach(static fn (Factory $factory): int => $factory->getGeode())
            ->max();
    }
}
