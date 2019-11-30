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
        $scenario = new Scenario(Integers::any());

        $called = 0;
        $scenario->then(static function(int $foo) use (&$called): void {
            ++$called;
        });

        $this->assertSame(100, $called);
    }

    public function testCallingWithMultipleSets()
    {
        $scenario = new Scenario(Integers::any(), Integers::any());

        $called = 0;
        $scenario->then(static function(int $a, $b) use (&$called): void {
            ++$called;
        });

        $this->assertSame(100, $called);
    }

    public function testAllowToOnlyTakeACertainNumberOfScenarios()
    {
        $scenario1 = new Scenario(Integers::any(), Integers::any());
        $scenario2 = $scenario1->take(10);

        $this->assertNotSame($scenario1, $scenario2);
        $this->assertInstanceOf(Scenario::class, $scenario2);

        $called1 = 0;
        $called2 = 0;
        $scenario1->then(static function(int $a, $b) use (&$called1): void {
            ++$called1;
        });
        $scenario2->then(static function(int $a, $b) use (&$called2): void {
            ++$called2;
        });

        $this->assertSame(100, $called1);
        $this->assertSame(10, $called2);
    }

    public function testAllowAFilterCanBeAppliedOnTheScenario()
    {
        $scenario1 = new Scenario(Integers::any(), Integers::any());
        $scenario2 = $scenario1->filter(static function($value): bool {
            return ($value[0] + $value[1]) % 2 === 0;
        });

        $this->assertNotSame($scenario1, $scenario2);
        $this->assertInstanceOf(Scenario::class, $scenario2);

        $additions1 = [];
        $additions2 = [];
        $scenario1->then(static function(int $a, $b) use (&$additions1): void {
            $additions1[] = ($a + $b) % 2;
        });
        $scenario2->then(static function(int $a, $b) use (&$additions2): void {
            $additions2[] = ($a + $b) % 2;
        });

        $this->assertCount(100, $additions1);
        // less because the composite is at max 100 and the filter is applied
        // after the generation so it can only be lower
        $this->assertLessThanOrEqual(100, \count($additions2));
        $this->assertSame(1, max($additions1));
        $this->assertSame(0, max($additions2));
    }
}
