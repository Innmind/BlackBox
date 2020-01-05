<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\Set\Value;
use PHPUnit\Framework\TestCase;

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
        $value = Value::mutable(fn() => new \stdClass);

        $this->assertInstanceOf(Value::class, $value);
        $this->assertFalse($value->isImmutable());
        $this->assertInstanceOf(\stdClass::class, $value->unwrap());
        $this->assertNotSame($value->unwrap(), $value->unwrap());
    }
}
