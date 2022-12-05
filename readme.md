# Advent of Code

This repository contains my [Advent of Code](https://adventofcode.com/) solutions in PHP.

## How to use
### Generate solution template

It is useful to immediately start solving puzzle. It creates all required files for puzzle. Default day: today.

```bash
php app.php app:generate-template [--year=2022] [--day=1]
```

![generate-template-command-outcome.png](resources/generate-template-command-outcome.png)

### Solve puzzle

It runs puzzle solver for given day and compare it with expected results.

```bash
php app.php app:solve [--year=2022] [--day=1] [--puzzle]
```

#### Good result
![solve-command-good-result.png](resources/solve-command-good-result.png)

#### Wrong result
![solve-command-wrong-result.png](resources/solve-command-wrong-result.png)

### List all puzzles and check results
It displays table with all puzzles for given year(default: current year). Click on puzzle name opens browser with puzzle description.

```bash
php app.php app:list [--year=2022]
```
![list-command.png](resources/list-command.png)
