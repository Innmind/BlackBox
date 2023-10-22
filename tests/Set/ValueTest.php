<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Value,
    Set\Dichotomy,
    PHPUnit\Framework\TestCase,
};

class ValueTest extends TestCase
{
    public function testAlwaysUnwrapTheSameImmutableValue()
    {
        $object = new \stdClass;
        $value = Value::immutable($object);

        $this->assertInstanceOf(Value::class, $value);
        $this->assertSame($object, $value->unwrap());
        $this->assertSame($object, $value->unwrap());
        $this->assertTrue($value->isImmutable());
    }

    public function testANewMutableValueIsGeneratedEachTimeItsAccessed()
    {
        $value = Value::mutable(static fn() => new \stdClass);

        $this->assertInstanceOf(Value::class, $value);
        $this->assertFalse($value->isImmutable());
        $this->assertInstanceOf(\stdClass::class, $value->unwrap());
        $this->assertNotSame($value->unwrap(), $value->unwrap());
    }

    public function testValueNotShinkrableWhenNoDichotomyGiven()
    {
        $this->assertFalse(Value::immutable(new \stdClass)->shrinkable());
        $this->assertFalse(Value::mutable(static fn() => new \stdClass)->shrinkable());

        $immutable = Value::immutable(new \stdClass, new Dichotomy(
            static fn() => Value::immutable(new \stdClass),
            static fn() => Value::immutable(new \stdClass),
        ));
        $mutable = Value::mutable(static fn() => new \stdClass, new Dichotomy(
            static fn() => Value::mutable(static fn() => new \stdClass),
            static fn() => Value::mutable(static fn() => new \stdClass),
        ));

        $this->assertTrue($immutable->shrinkable());
        $this->assertTrue($mutable->shrinkable());
    }

    public function testShrinkReturnTheGivenDichotomy()
    {
        $immutable = Value::immutable(new \stdClass, $expectedImmutable = new Dichotomy(
            static fn() => Value::immutable(new \stdClass),
            static fn() => Value::immutable(new \stdClass),
        ));
        $mutable = Value::mutable(static fn() => new \stdClass, $expectedMutable = new Dichotomy(
            static fn() => Value::mutable(static fn() => new \stdClass),
            static fn() => Value::mutable(static fn() => new \stdClass),
        ));

        $this->assertSame($expectedImmutable, $immutable->shrink());
        $this->assertSame($expectedMutable, $mutable->shrink());
    }
}
