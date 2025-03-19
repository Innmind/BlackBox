<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
    Set\Dichotomy,
    Set\Value,
    PHPUnit\Framework\TestCase,
};

class DichotomyTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this
            ->forAll(
                Set\Either::any(
                    Set\Integers::any(),
                    Set\Strings::any(),
                ),
                Set\Either::any(
                    Set\Integers::any(),
                    Set\Strings::any(),
                ),
            )
            ->then(function($a, $b) {
                $expectedA = Value::of($a);
                $expectedB = Value::of($b);

                $dichotomy = Dichotomy::of(
                    $expectedA,
                    $expectedB,
                );

                $this->assertSame($expectedA, $dichotomy->a());
                $this->assertSame($expectedB, $dichotomy->b());
            });
    }
}
