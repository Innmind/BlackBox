<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Given,
    Given\Any,
    Given\InitialValue\Name,
    Given\Scenario,
};
use Innmind\Immutable\{
    Set,
    StreamInterface,
};
use PHPUnit\Framework\TestCase;

class GivenTest extends TestCase
{
    public function testMatrix()
    {
        $given = new Given(
            new Any(new Name('a'), Set::of('int', 1, 2)),
            new Any(new Name('b'), Set::of('int', 3, 4, 5))
        );

        $scenarios = $given->matrix();

        // [1, 3]
        // [2, 3]
        // [1, 4]
        // [2, 4]
        // [1, 5]
        // [2, 5]

        $this->assertInstanceOf(StreamInterface::class, $scenarios);
        $this->assertSame(Scenario::class, (string) $scenarios->type());
        $this->assertCount(6, $scenarios);
        $this->assertSame(1, $scenarios->get(0)->a);
        $this->assertSame(3, $scenarios->get(0)->b);
        $this->assertSame(2, $scenarios->get(1)->a);
        $this->assertSame(3, $scenarios->get(1)->b);
        $this->assertSame(1, $scenarios->get(2)->a);
        $this->assertSame(4, $scenarios->get(2)->b);
        $this->assertSame(2, $scenarios->get(3)->a);
        $this->assertSame(4, $scenarios->get(3)->b);
        $this->assertSame(1, $scenarios->get(4)->a);
        $this->assertSame(5, $scenarios->get(4)->b);
        $this->assertSame(2, $scenarios->get(5)->a);
        $this->assertSame(5, $scenarios->get(5)->b);
    }
}
