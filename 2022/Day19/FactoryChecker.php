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
        $maxGeoCode = 0;

        while ($minute <= $maxMinutes) {
            $newFactories = [];
            $newMaxGeocode = 0;
            foreach ($factories as $otherFactory) {
                if ($maxGeoCode <= $otherFactory->getGeode()) {
                    $newFactories = [...$newFactories, ...$otherFactory->clone($minute)];
                    $newMaxGeocode = max($newMaxGeocode, $otherFactory->getGeode());
                }
            }

            foreach ($newFactories as $tmp) {
                $geode = $tmp->robots['geode'];

                if ($geode > 0) {
                    $test = 'test';
                }
            }

            foreach ($newFactories as $tmp) {
                $geode = $tmp->inventory['geode'];

                if ($geode > 0) {
                    $test = 'test';
                }
            }

            if ($minute === 1) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
                    && $tmp->robots === ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 2) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 3) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 0, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 1, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 4) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 1, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 1, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 5) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 2, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 2, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 6) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 4, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 2, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 7) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 6, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 3, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 8) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 9, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 3, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 9) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 3, 'clay' => 12, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 3, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 10) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 4, 'clay' => 15, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 3, 'obsidian' => 0, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 11) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 4, 'obsidian' => 0, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 3, 'obsidian' => 1, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 12) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 7, 'obsidian' => 1, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 1, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 13) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 11, 'obsidian' => 2, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 1, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 14) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 3, 'clay' => 15, 'obsidian' => 3, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 1, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }
            if ($minute === 15) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 1, 'clay' => 5, 'obsidian' => 4, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }
            if ($minute === 16) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 9, 'obsidian' => 6, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }
            if ($minute === 17) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 3, 'clay' => 13, 'obsidian' => 8, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 0]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 18) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 2, 'clay' => 17, 'obsidian' => 3, 'geode' => 0]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 1]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 19) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 3, 'clay' => 21, 'obsidian' => 5, 'geode' => 1]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 1]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 20) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 4, 'clay' => 25, 'obsidian' => 7, 'geode' => 2]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 1]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 21) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 3, 'clay' => 29, 'obsidian' => 2, 'geode' => 3]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 2]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 22) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 4, 'clay' => 33, 'obsidian' => 4, 'geode' => 5]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 2]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 23) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 5, 'clay' => 37, 'obsidian' => 6, 'geode' => 7]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 2]
                    ) {
                        $count++;
                    }
                }
            }

            if ($minute === 24) {
                $count = 0;
                foreach ($newFactories as $tmp) {
                    if ($tmp->inventory === ['ore' => 6, 'clay' => 41, 'obsidian' => 8, 'geode' => 9]
                        && $tmp->robots === ['ore' => 1, 'clay' => 4, 'obsidian' => 2, 'geode' => 2]
                    ) {
                        $count++;
                    }
                }
            }


            $maxGeoCode = max($newMaxGeocode, $maxGeoCode);

            $factories = array_unique($newFactories);
            C::writeln();
            C::writeln('Minute: '. $minute);
            C::writeln('Factories: '. count($newFactories));
            C::writeln('Max geocode: ' . $maxGeoCode);
            $minute++;
        }

        return Collection::create($factories)
            ->forEach(static fn (Factory $factory): int => $factory->getGeode())
            ->max();
    }
}
