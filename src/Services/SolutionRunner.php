<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\FileNotFound;
use App\Input;
use App\InputType;
use App\Result;
use App\ResultPair;
use App\Solver;
use App\SolverFullyQualifiedClassname;

final class SolutionRunner
{
    public function __construct(
        private readonly FileLoader $fileLoader
    ) {
    }

    /**
     * @throws FileNotFound
     */
    public function run(Solver $solution, InputType $inputType): ResultPair
    {
        $date = SolverFullyQualifiedClassname::fromObject($solution)->getDate();

        $inputFileContent = $this->fileLoader->loadInput($date, $inputType);
        $expectedResultFileContent = $this->fileLoader->loadExpectedOutput($date, $inputType);

        $expectedResult = Result::fromArray($expectedResultFileContent);
        $inputFile = Input::fromArray($inputFileContent);

        $result = $solution->solve($inputFile);

        return new ResultPair($result, $expectedResult);
    }
}
