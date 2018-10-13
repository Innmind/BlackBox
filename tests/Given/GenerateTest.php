<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\Generate,
    Given\Any,
    Given\InitialValue,
    Given\InitialValue\Name,
    Given\SoFar,
    Exception\LogicException,
};
use Innmind\Immutable\{
    Set,
    StreamInterface,
    Exception\ElementNotFoundException,
};
use PHPUnit\Framework\TestCase;

class GenerateTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            InitialValue::class,
            new Generate(new Name('foo'), function(){})
        );
    }

    public function testDependOn()
    {
        $generate = new Generate(
            new Name('foo'),
            function(){}
        );

        $this->assertNotSame(
            $generate,
            $generate->dependOn($this->createMock(InitialValue::class))
        );
        $this->assertInstanceOf(
            Generate::class,
            $generate->dependOn($this->createMock(InitialValue::class))
        );
    }

    public function testThrowWhenAlreadyDependOnOtherSet()
    {
        $generate = new Generate(
            new Name('foo'),
            function(){}
        );

        $this->expectException(LogicException::class);

        $generate
            ->dependOn($this->createMock(InitialValue::class))
            ->dependOn($this->createMock(InitialValue::class));
    }

    public function testGeneration()
    {
        $generate = new Generate(
            new Name('foo'),
            function() {
                return 42;
            }
        );

        $sets = $generate->sets();

        $this->assertInstanceOf(StreamInterface::class, $sets);
        $this->assertSame(SoFar::class, (string) $sets->type());
        $this->assertCount(1, $sets);
        $this->assertSame(42, $sets->current()->foo);
    }

    public function testGenerationWithADependency()
    {
        $any = new Any(
            new Name('foo'),
            Set::of('mixed', 42, 'bar')
        );
        $generate = new Generate(
            new Name('baz'),
            function(SoFar $given) {
                return $given->foo;
            }
        );

        $generate2 = $generate->dependOn($any);

        $this->assertCount(2, $any->sets());

        try {
            $this->assertCount(2, $generate->sets());
            $this->fail('it should throw accessing unknown foo dependency');
        } catch (ElementNotFoundException $e) {
            //pass
        }

        $sets = $generate2->sets();

        $this->assertCount(2, $sets);
        $this->assertSame(42, $sets->current()->foo);
        $this->assertSame(42, $sets->current()->baz);
        $sets->next();
        $this->assertSame('bar', $sets->current()->foo);
        $this->assertSame('bar', $sets->current()->baz);
    }
}
