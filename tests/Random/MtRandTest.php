<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Random;

use Innmind\BlackBox\{
    Random\MtRand,
    Random,
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

class MtRandTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(Random::class, new MtRand);
    }

    public function testLowerBoundIsAlwaysRespected()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($min) {
                $this->assertGreaterThanOrEqual(
                    $min,
                    (new MtRand)($min, \PHP_INT_MAX),
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
                    (new MtRand)(\PHP_INT_MIN, $max),
                );
            });
    }

    public function testUnseededGeneratorNeverReturnTheSameValueTwice()
    {
        $rand = new MtRand;

        $this->assertNotSame(
            $rand(\PHP_INT_MIN, \PHP_INT_MAX),
            $rand(\PHP_INT_MIN, \PHP_INT_MAX),
        );
    }

    public function testSeededGeneratorAlwaysReturnTheSameValue()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($seed) {
                $rand = MtRand::seed($seed);

                $this->assertSame(
                    $rand(\PHP_INT_MIN, \PHP_INT_MAX),
                    $rand(\PHP_INT_MIN, \PHP_INT_MAX),
                );
            });
    }

    public function testMtRandFunctionIsNotAffectedByTheSeeding()
    {
        $this
            ->forAll(Set\Integers::any())
            ->then(function($seed) {
                $rand = MtRand::seed($seed);

                $this->assertNotSame(
                    $rand(\PHP_INT_MIN, \PHP_INT_MAX),
                    \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX),
                );
                $this->assertNotSame(
                    \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX),
                    \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX),
                );
            });
    }
}
