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
use Innmind\Immutable\Set;
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
        $this->assertInstanceOf(
            Any::class,
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

        $this->assertInstanceOf(\Generator::class, $sets);
        $this->assertSame(42, $sets->current()->foo);
        $sets->next();
        $this->assertSame('bar', $sets->current()->foo);
    }

    public function testGenerationWithADependency()
    {
        $any = new Any(
            new Name('foo'),
            (function() {
                yield 42;
                yield 'bar';
            })()
        );
        $any2 = new Any(
            new Name('baz'),
            (function() {
                yield 1;
                yield 2;
            })()
        );

        $sets = $any->dependOn($any2)->sets();

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
        $sets->next();
        $this->assertFalse($sets->valid());
    }
}
