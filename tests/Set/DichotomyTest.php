<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class DichotomyTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this
            ->forAll(
                new Set\Either(
                    Set\Integers::any(),
                    Set\Strings::any(),
                ),
                new Set\Either(
                    Set\Integers::any(),
                    Set\Strings::any(),
                ),
            )
            ->then(function($a, $b) {
                $expectedA = Value::immutable($a);
                $expectedB = Value::immutable($b);

                $dichotomy = new Dichotomy(
                    fn() => $expectedA,
                    fn() => $expectedB,
                );

                $this->assertSame($expectedA, $dichotomy->a());
                $this->assertSame($expectedB, $dichotomy->b());
            });
    }
}
