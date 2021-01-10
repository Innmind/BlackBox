<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Seeder,
    Random\RandomInt,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use ReverseRegex\Lexer;

class SeederTest extends TestCase
{
    use BlackBox;

    public function testAlwaysReturnADifferentValue()
    {
        if (!\class_exists(Lexer::class)) {
            $this->markTestSkipped();
        }

        $this
            ->forAll(Set\Elements::of(
                Set\Unicode::strings(),
                Set\Email::any(),
                Set\Strings::any(),
                Set\Uuid::any(),
            ))
            ->then(function($set) {
                $seeder = new Seeder(new RandomInt);

                $this->assertIsString($seeder($set));
                $this->assertNotSame(
                    $seeder($set),
                    $seeder($set),
                );
            });
    }
}
