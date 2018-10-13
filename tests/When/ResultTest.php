<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\When;

use Innmind\BlackBox\When\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    /**
     * @dataProvider values
     */
    public function testValue($value)
    {
        $result = new Result($value);

        $this->assertSame($value, $result->value());
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
