<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use function Innmind\BlackBox\{
    given,
    any,
    value,
    generate,
    when,
    then,
    test,
};
use Innmind\BlackBox\{
    Given,
    Given\Any,
    Given\Generate,
    Given\Scenario,
    When,
    Then,
    Test,
    Assert,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\{
    Set,
    Map,
};
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    public function testValue()
    {
        $value = value('a', 1);

        $this->assertInstanceOf(Any::class, $value);
        $this->assertCount(1, $value->sets());
        $this->assertSame(1, $value->sets()->current()->a);
    }

    public function testAny()
    {
        $any = any('a', Set::of('int', 1));

        $this->assertInstanceOf(Any::class, $any);
        $this->assertCount(1, $any->sets());
        $this->assertSame(1, $any->sets()->current()->a);
    }

    public function testGenerate()
    {
        $generate = generate('a', function() {
            return 42;
        });

        $this->assertInstanceOf(Generate::class, $generate);
        $this->assertCount(1, $generate->sets());
        $this->assertSame(42, $generate->sets()->current()->a);
    }

    public function testGiven()
    {
        $given = given(value('a', 1));

        $this->assertInstanceOf(Given::class, $given);
        $scenarios = $given->scenarios();
        $this->assertSame(1, $scenarios->current()->a);
        $scenarios->next();
        $this->assertFalse($scenarios->valid());
    }

    public function testWhen()
    {
        $os = $this->createMock(OperatingSystem::class);
        $when = when(function() {
            return 42;
        });

        $this->assertInstanceOf(When::class, $when);
        $this->assertSame(
            42,
            $when($os, new Scenario(new Map('string', 'mixed')))->value()
        );
    }

    public function testThen()
    {
        $this->assertInstanceOf(Then::class, then(Assert\int()));
    }

    public function testTest()
    {
        $test = test('foo', given(), when(function(){}), then(Assert\int()));

        $this->assertInstanceOf(Test::class, $test);
    }
}
