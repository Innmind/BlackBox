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
    public function testScenarios()
    {
        $given = new Given(
            new Any(new Name('a'), Set::of('int', 1, 2)),
            new Any(new Name('b'), Set::of('int', 3, 4, 5))
        );

        $scenarios = $given->scenarios();

        // [1, 3]
        // [2, 3]
        // [1, 4]
        // [2, 4]
        // [1, 5]
        // [2, 5]

        $this->assertInstanceOf(\Generator::class, $scenarios);
        $this->assertSame(1, $scenarios->current()->a);
        $this->assertSame(3, $scenarios->current()->b);
        $scenarios->next();
        $this->assertSame(2, $scenarios->current()->a);
        $this->assertSame(3, $scenarios->current()->b);
        $scenarios->next();
        $this->assertSame(1, $scenarios->current()->a);
        $this->assertSame(4, $scenarios->current()->b);
        $scenarios->next();
        $this->assertSame(2, $scenarios->current()->a);
        $this->assertSame(4, $scenarios->current()->b);
        $scenarios->next();
        $this->assertSame(1, $scenarios->current()->a);
        $this->assertSame(5, $scenarios->current()->b);
        $scenarios->next();
        $this->assertSame(2, $scenarios->current()->a);
        $this->assertSame(5, $scenarios->current()->b);
        $scenarios->next();
        $this->assertFalse($scenarios->valid());
    }

    public function testEmptyMatrix()
    {
        $given = new Given;

        $this->assertCount(1, $given->scenarios());
    }

    public function testOneDependency()
    {
        $given = new Given(
            new Any(new Name('a'), Set::of('int', 1))
        );

        $scenarios = $given->scenarios();
        $this->assertSame(1, $scenarios->current()->a);
        $scenarios->next();
        $this->assertFalse($scenarios->valid());
    }
}
