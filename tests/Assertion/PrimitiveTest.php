<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Assertion;

use Innmind\BlackBox\{
    Assertion\Primitive,
    Assertion,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
    Exception\LogicException,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class PrimitiveTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Assertion::class, new Primitive('int'));
    }

    /**
     * @dataProvider types
     */
    public function testInvokation($type, $good, $bad)
    {
        $assert = new Primitive($type);

        $report = $assert(
            new ScenarioReport,
            new Result($good),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());

        $report = $assert(
            new ScenarioReport,
            new Result($bad),
            new Scenario(new Map('string', 'mixed'))
        );

        $this->assertTrue($report->failed());
        $this->assertSame(1, $report->assertions());
        $this->assertSame("Not a $type", $report->failure());
    }

    public function testThrowWhenTypeNotAPrimitive()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('foo is not a primitive');

        new Primitive('foo');
    }

    public function types(): array
    {
        return [
            ['null', null, ''],
            ['bool', false, 0],
            ['int', 42, '42'],
            ['float', 42.24, '42.24'],
            ['string', 'foo', 42],
            ['array', [], ''],
            ['iterable', [], ''],
            ['callable', function(){}, new \stdClass],
            ['resource', \tmpfile(), ''],
            ['object', new class {}, null],
        ];
    }
}
