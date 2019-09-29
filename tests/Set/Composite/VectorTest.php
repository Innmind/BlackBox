<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\{
    Set\Composite\Vector,
    Set\Composite\Matrix,
    Set\FromGenerator,
};
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function testDot()
    {
        $a = new Vector('a', 'b');
        $b = new Vector('c', 'd');

        $matrix = $a->dot($b);

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            \iterator_to_array($matrix)
        );
    }

    public function testOf()
    {
        $a = Vector::of(FromGenerator::of(function() {
            yield 'a';
            yield 'b';
        }));
        $b = Vector::of(FromGenerator::of(function() {
            yield 'c';
            yield 'd';
        }));

        $this->assertInstanceOf(Vector::class, $a);
        $this->assertInstanceOf(Vector::class, $b);

        $matrix = $a->dot($b);

        $this->assertInstanceOf(Matrix::class, $matrix);
        $this->assertSame(
            [
                ['a', 'c'],
                ['a', 'd'],
                ['b', 'c'],
                ['b', 'd'],
            ],
            \iterator_to_array($matrix)
        );
    }
}
