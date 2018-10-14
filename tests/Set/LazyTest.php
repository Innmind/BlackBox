<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\Set\Lazy;
use Innmind\Immutable\{
    SetInterface,
    Set,
};
use PHPUnit\Framework\TestCase;

class LazyTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(SetInterface::class, new Lazy('int'));
    }

    public function testEmptyIterator()
    {
        $this->assertSame(
            [],
            iterator_to_array(new Lazy('int'))
        );
    }

    public function testIterator()
    {
        $lazy = Lazy::of('int', function() {
            return Set::of('int', 1, 2, 3);
        });

        $this->assertSame([1, 2, 3], iterator_to_array($lazy));
    }

    public function testNotLoadedWhenNotUsed()
    {
        $this->assertInstanceOf(
            Lazy::class,
            Lazy::of('int', function() {
                return Set::of('int', ...range(-INF, INF));
            })
        );
    }

    public function testReduce()
    {
        $lazy = Lazy::of('int', function() {
            return Set::of('int', 1, 2, 3);
        });

        $this->assertSame(
            6,
            $lazy->reduce(
                0,
                static function($carry, $i): int {
                    return $carry += $i;
                }
            )
        );
    }
}
