<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    When,
    When\Result,
    Given\Scenario,
};
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class WhenTest extends TestCase
{
    public function testInvokation()
    {
        $expected = new Scenario(new Map('string', 'mixed'));

        $when = new When(function($scenrario) use ($expected) {
            if ($scenrario !== $expected) {
                throw new \Exception;
            }

            return 42;
        });

        $this->assertInstanceOf(Result::class, $when($expected));
        $this->assertSame(42, $when($expected)->value());
    }

    public function testThrowWhenTryingToAccessThisInsideTheCallable()
    {
        $when = new When(function() {
            $this->assertSame(42, 42);
        });

        $result = $when(new Scenario(new Map('string', 'mixed')));

        $this->assertInstanceOf(
            \Error::class,
            $result->value()
        );
        $this->assertSame(
            'Call to undefined method class@anonymous::assertSame()',
            $result->value()->getMessage()
        );
    }
}
