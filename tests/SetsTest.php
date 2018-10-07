<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use function Innmind\BlackBox\Set\{
    integers,
    integersExceptZero,
    naturalNumbers,
    naturalNumbersExceptZero,
    realNumbers,
    range,
    chars,
    strings,
    unsafeStrings,
};
use Innmind\BlackBox\Exception\LogicException;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class SetsTest extends TestCase
{
    public function testIntegers()
    {
        $set = integers();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(1000, $set);

        $this->assertFalse($set->equals(integers()));
    }

    public function testThrowWhenIntegersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integers(0);
    }

    public function testIntegersExceptZero()
    {
        $set = integersExceptZero();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(1000, $set);
        $this->assertFalse($set->contains(0));

        $this->assertFalse($set->equals(integersExceptZero()));
    }

    public function testThrowWhenIntegersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integersExceptZero(0);
    }

    public function testNaturalNumbers()
    {
        $set = naturalNumbers();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(1000, $set);
        $lowest = $set
            ->sort(static function(int $a, int $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest >= 0);

        $this->assertFalse($set->equals(naturalNumbers()));
    }

    public function testThrowWhenNaturalNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbers(0);
    }

    public function testNaturalNumbersExceptZero()
    {
        $set = naturalNumbersExceptZero();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('int', (string) $set->type());
        $this->assertCount(1000, $set);
        $lowest = $set
            ->sort(static function(int $a, int $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest >= 1);

        $this->assertFalse($set->equals(naturalNumbersExceptZero()));
    }

    public function testThrowWhenNaturalNumbersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbersExceptZero(0);
    }

    public function testRealNumbers()
    {
        $set = realNumbers();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('float', (string) $set->type());
        $this->assertCount(1000, $set);
        $lowest = $set
            ->sort(static function(float $a, float $b): int {
                return $a <=> $b;
            })
            ->first();
        $this->assertTrue($lowest < 0);

        $this->assertFalse($set->equals(realNumbers()));
    }

    public function testThrowWhenRealNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        realNumbers(0);
    }

    public function testRange()
    {
        $set = range(-100, 99, 2);

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('float', (string) $set->type());
        $this->assertCount(100, $set);
    }

    public function testChar()
    {
        $set = chars();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(256, $set);

        $this->assertTrue($set->equals(chars()));
    }

    public function testStrings()
    {
        $set = strings();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
        $this->assertCount(1000, $set);
    }

    public function testThrowWhenStringsRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        strings(0);
    }

    public function testUnsafeStrings()
    {
        $set = unsafeStrings();

        $this->assertInstanceOf(SetInterface::class, $set);
        $this->assertSame('string', (string) $set->type());
    }
}
