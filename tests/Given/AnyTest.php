<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\Any,
    Given\InitialValue,
    Given\InitialValue\Name,
    Given\SoFar,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Set,
    StreamInterface,
};
use PHPUnit\Framework\TestCase;

class AnyTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            InitialValue::class,
            new Any(new Name('foo'), Set::of('mixed'))
        );
    }

    public function testDependOn()
    {
        $any = new Any(
            new Name('foo'),
            Set::of('mixed', 42, 'bar')
        );

        $this->assertNotSame(
            $any,
            $any->dependOn($this->createMock(InitialValue::class))
        );
    }

    public function testThrowWhenAlreadyDependOnOtherSet()
    {
        $any = new Any(
            new Name('foo'),
            Set::of('mixed', 42, 'bar')
        );

        $this->expectException(LogicException::class);

        $any
            ->dependOn($this->createMock(InitialValue::class))
            ->dependOn($this->createMock(InitialValue::class));
    }

    public function testGeneration()
    {
        $any = new Any(
            new Name('foo'),
            Set::of('mixed', 42, 'bar')
        );

        $sets = $any->sets();

        $this->assertInstanceOf(StreamInterface::class, $sets);
        $this->assertSame(SoFar::class, (string) $sets->type());
        $this->assertCount(2, $sets);
        $this->assertSame(42, $sets->current()->foo);
        $sets->next();
        $this->assertSame('bar', $sets->current()->foo);
    }

    public function testGenerationWithADependency()
    {
        $any = new Any(
            new Name('foo'),
            Set::of('mixed', 42, 'bar')
        );
        $any2 = new Any(
            new Name('baz'),
            Set::of('mixed', 1, 2)
        );

        $any3 = $any->dependOn($any2);

        $this->assertCount(2, $any->sets());
        $this->assertCount(2, $any2->sets());

        $sets = $any3->sets();

        $this->assertCount(4, $sets);
        $this->assertSame(42, $sets->current()->foo);
        $this->assertSame(1, $sets->current()->baz);
        $sets->next();
        $this->assertSame(42, $sets->current()->foo);
        $this->assertSame(2, $sets->current()->baz);
        $sets->next();
        $this->assertSame('bar', $sets->current()->foo);
        $this->assertSame(1, $sets->current()->baz);
        $sets->next();
        $this->assertSame('bar', $sets->current()->foo);
        $this->assertSame(2, $sets->current()->baz);
    }
}
