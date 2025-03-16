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
        $value = Value::of($object);

        $this->assertInstanceOf(Value::class, $value);
        $this->assertSame($object, $value->unwrap());
        $this->assertSame($object, $value->unwrap());
        $this->assertTrue($value->isImmutable());
    }

    public function testValueNotShinkrableWhenNoDichotomyGiven()
    {
        $this->assertNull(Value::of(new \stdClass)->shrink());
        $this->assertNull(
            Value::of(new \stdClass)
                ->mutable(true)
                ->shrink(),
        );

        $immutable = Value::of(new \stdClass)->shrinkWith(static fn() => Dichotomy::of(
            Value::of(new \stdClass),
            Value::of(new \stdClass),
        ));
        $mutable = Value::of(new \stdClass)
            ->mutable(true)
            ->shrinkWith(static fn() => Dichotomy::of(
                Value::of(new \stdClass)->mutable(true),
                Value::of(new \stdClass)->mutable(true),
            ));

        $this->assertNotNull($immutable->shrink());
        $this->assertNotNull($mutable->shrink());
    }

    public function testShrinkReturnTheGivenDichotomy()
    {
        $expectedImmutable = Dichotomy::of(
            Value::of(new \stdClass),
            Value::of(new \stdClass),
        );
        $expectedMutable = Dichotomy::of(
            Value::of(new \stdClass)->mutable(true),
            Value::of(new \stdClass)->mutable(true),
        );
        $immutable = Value::of(new \stdClass)->shrinkWith(static fn() => $expectedImmutable);
        $mutable = Value::of(new \stdClass)
            ->mutable(true)
            ->shrinkWith(static fn() => $expectedMutable);

        $this->assertSame($expectedImmutable->a(), $immutable->shrink()->a());
        $this->assertSame($expectedImmutable->b(), $immutable->shrink()->b());
        $this->assertSame($expectedMutable->a(), $mutable->shrink()->a());
        $this->assertSame($expectedMutable->b(), $mutable->shrink()->b());
    }
}
