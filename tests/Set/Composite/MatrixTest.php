<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Composite\Matrix,
    Composite\Combination,
    FromGenerator,
};
use PHPUnit\Framework\TestCase;

class MatrixTest extends TestCase
{
    public function testInterface()
    {
        $matrix = new Matrix(
            new Combination('a', 'b'),
            new Combination('a', 'c'),
            new Combination('b', 'a'),
            new Combination('b', 'c')
        );

        $this->assertInstanceOf(\Iterator::class, $matrix);
        $this->assertSame(
            [
                ['a', 'b'],
                ['a', 'c'],
                ['b', 'a'],
                ['b', 'c'],
            ],
            \iterator_to_array($matrix)
        );
    }

    public function testDot()
    {
        $matrix = new Matrix(
            new Combination('a', 'b'),
            new Combination('a', 'c'),
            new Combination('b', 'a'),
            new Combination('b', 'c')
        );
        $matrix2 = $matrix->dot(FromGenerator::of(
            'foo',
            function() {
                yield 'e';
                yield 'f';
            }
        ));

        $this->assertInstanceOf(Matrix::class, $matrix2);
        $this->assertNotSame($matrix, $matrix2);
        $this->assertSame(
            [
                ['a', 'b'],
                ['a', 'c'],
                ['b', 'a'],
                ['b', 'c'],
            ],
            \iterator_to_array($matrix)
        );
        $this->assertSame(
            [
                ['e', 'a', 'b'],
                ['e', 'a', 'c'],
                ['e', 'b', 'a'],
                ['e', 'b', 'c'],
                ['f', 'a', 'b'],
                ['f', 'a', 'c'],
                ['f', 'b', 'a'],
                ['f', 'b', 'c'],
            ],
            \iterator_to_array($matrix2)
        );
    }
}
