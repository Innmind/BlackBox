<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Random;

use Innmind\BlackBox\{
    Random\RandomInt,
    Random,
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

class RandomIntTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(Random::class, new RandomInt);
    }

    public function testLowerBoundIsAlwaysRespected()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($min) {
                $this->assertGreaterThanOrEqual(
                    $min,
                    (new RandomInt)($min, \PHP_INT_MAX),
                );
            });
    }

    public function testUpperBoundIsAlwaysRespected()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($max) {
                $this->assertLessThanOrEqual(
                    $max,
                    (new RandomInt)(\PHP_INT_MIN, $max),
                );
            });
    }

    public function testGeneratorNeverReturnTheSameValueTwice()
    {
        $rand = new RandomInt;

        $this->assertNotSame(
            $rand(\PHP_INT_MIN, \PHP_INT_MAX),
            $rand(\PHP_INT_MIN, \PHP_INT_MAX),
        );
    }
}
