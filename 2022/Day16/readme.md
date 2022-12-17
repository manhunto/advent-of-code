## What have I learnt?

- **attempt 1**: generating every possible path for 10 nodes and 30 moves is very time and memory consuming
- **attempt 2**: moving only to valves that can be opened speeds up the algorithm considerably and allows you to solve the example input, but generating all possible paths for the puzzle input still is too slow. The added check that each valve can only be opened once also reduces the execution time of the algorithm
- reduce paths to only those that are doable within 30 minutes reduces algorithm to ~4 sec
- calculate for O(n^2) algorithms once before nested loops
