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

    public function testValueNotShinkrableWhenNoDichotomyGiven()
    {
        $this->assertNull(Value::immutable(new \stdClass)->shrink());
        $this->assertNull(
            Value::immutable(new \stdClass)
                ->flagMutable(true)
                ->shrink(),
        );

        $immutable = Value::immutable(new \stdClass)->shrinkWith(static fn() => Dichotomy::of(
            Value::immutable(new \stdClass),
            Value::immutable(new \stdClass),
        ));
        $mutable = Value::immutable(new \stdClass)
            ->flagMutable(true)
            ->shrinkWith(static fn() => Dichotomy::of(
                Value::immutable(new \stdClass)->flagMutable(true),
                Value::immutable(new \stdClass)->flagMutable(true),
            ));

        $this->assertNotNull($immutable->shrink());
        $this->assertNotNull($mutable->shrink());
    }

    public function testShrinkReturnTheGivenDichotomy()
    {
        $expectedImmutable = Dichotomy::of(
            Value::immutable(new \stdClass),
            Value::immutable(new \stdClass),
        );
        $expectedMutable = Dichotomy::of(
            Value::immutable(new \stdClass)->flagMutable(true),
            Value::immutable(new \stdClass)->flagMutable(true),
        );
        $immutable = Value::immutable(new \stdClass)->shrinkWith(static fn() => $expectedImmutable);
        $mutable = Value::immutable(new \stdClass)
            ->flagMutable(true)
            ->shrinkWith(static fn() => $expectedMutable);

        $this->assertSame($expectedImmutable->a(), $immutable->shrink()->a());
        $this->assertSame($expectedImmutable->b(), $immutable->shrink()->b());
        $this->assertSame($expectedMutable->a(), $mutable->shrink()->a());
        $this->assertSame($expectedMutable->b(), $mutable->shrink()->b());
    }
}
