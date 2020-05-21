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

class SeederTest extends TestCase
{
    use BlackBox;

    public function testAlwaysReturnADifferentValue()
    {
        $this
            ->forAll(Set\Elements::of(
                Set\Chars::any(),
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
