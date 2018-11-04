<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\When;

use Innmind\BlackBox\When\Result;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @dataProvider values
     */
    public function testValue($value)
    {
        $result = new Result(
            $value,
            $executionTime = $this->createMock(ElapsedPeriodInterface::class)
        );

        $this->assertSame($value, $result->value());
        $this->assertSame($executionTime, $result->executionTime());
    }

    public function values(): array
    {
        return [
            [42],
            [42.24],
            ['42.24'],
            [true],
            [false],
            [[]],
            [new class {}],
            [function(){}],
        ];
    }
}
