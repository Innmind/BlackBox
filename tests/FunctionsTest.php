<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use function Innmind\BlackBox\{
    given,
    any,
    value,
    generate,
    when,
};
use Innmind\BlackBox\{
    Given,
    Given\Any,
    Given\Generate,
    Given\Scenario,
    When,
};
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
        $this->assertCount(1, $given->scenarios());
        $this->assertSame(1, $given->scenarios()->current()->a);
    }

    public function testWhen()
    {
        $when = when(function() {
            return 42;
        });

        $this->assertInstanceOf(When::class, $when);
        $this->assertSame(42, $when(new Scenario(new Map('string', 'mixed')))->value());
    }
}
