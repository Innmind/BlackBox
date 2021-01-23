<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Runner,
    Runner\Printer,
    Random\RandomInt,
    Exception\LogicException,
};
use PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    public function testAnInvalidProofFileReturnTypeGeneratesAnError()
    {
        $runner = new Runner(
            100,
            true,
            new RandomInt,
            $this->createMock(Printer::class),
            '',
        );

        $this->expectException(LogicException::class);

        $runner('proofs/invalid.php');
    }

    public function testGeneratesAnErrorIfTheProofFileFunctionDoesntYieldProofs()
    {
        $runner = new Runner(
            100,
            true,
            new RandomInt,
            $this->createMock(Printer::class),
            '',
        );

        $this->expectException(LogicException::class);

        $runner('proofs/function.php');
    }
}
