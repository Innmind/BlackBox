<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    When,
    When\Result,
    Given\Scenario,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class WhenTest extends TestCase
{
    public function testInvokation()
    {
        $expected = new Scenario(new Map('string', 'mixed'));
        $os = $this->createMock(OperatingSystem::class);

        $when = new When(function($scenario) use ($expected) {
            if ($scenario !== $expected) {
                throw new \Exception;
            }

            return 42;
        });

        $this->assertInstanceOf(Result::class, $when($os, $expected));
        $this->assertSame(42, $when($os, $expected)->value());
    }

    public function testThrowWhenTryingToAccessThisInsideTheCallable()
    {
        $when = new When(function() {
            $this->assertSame(42, 42);
        });
        $os = $this->createMock(OperatingSystem::class);

        $result = $when($os, new Scenario(new Map('string', 'mixed')));

        $this->assertInstanceOf(
            \Error::class,
            $result->value()
        );
        $this->assertSame(
            'Using $this when not in object context',
            $result->value()->getMessage()
        );
    }
}
