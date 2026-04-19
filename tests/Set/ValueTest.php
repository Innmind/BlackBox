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
    }

    public function testValueNotShinkrableWhenNoDichotomyGiven()
    {
        $this->assertNull(Value::of(new \stdClass)->shrink());

        $immutable = Value::of(new \stdClass)->shrinkWith(
            new class implements Value\Shrinker {
                public function __invoke(Value $value): ?Dichotomy
                {
                    return Dichotomy::of(
                        Value::of(new \stdClass),
                        Value::of(new \stdClass),
                    );
                }
            },
        );

        $this->assertNotNull($immutable->shrink());
    }

    public function testShrinkReturnTheGivenDichotomy()
    {
        $expectedImmutable = Dichotomy::of(
            Value::of(new \stdClass),
            Value::of(new \stdClass),
        );
        $immutable = Value::of(new \stdClass)->shrinkWith(
            new class($expectedImmutable) implements Value\Shrinker {
                public function __construct(
                    private $dichotomy,
                ) {
                }

                public function __invoke(Value $value): ?Dichotomy
                {
                    return $this->dichotomy;
                }
            },
        );

        $this->assertSame($expectedImmutable->a(), $immutable->shrink()->a());
        $this->assertSame($expectedImmutable->b(), $immutable->shrink()->b());
    }
}
