<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Scenario,
    Set\Integers,
};
use PHPUnit\Framework\TestCase;

class ScenarioTest extends TestCase
{
    public function testCallingWithOnlyOneSet()
    {
        $scenario = new Scenario(Integers::of());

        $called = 0;
        $scenario->then(static function(int $foo) use (&$called): void {
            ++$called;
        });

        $this->assertSame(100, $called);
    }

    public function testCallingWithMultipleSets()
    {
        $scenario = new Scenario(Integers::of(), Integers::of());

        $called = 0;
        $scenario->then(static function(int $a, $b) use (&$called): void {
            ++$called;
        });

        $this->assertSame(10000, $called);
    }
}
