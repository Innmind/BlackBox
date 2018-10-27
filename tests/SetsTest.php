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
    mixed,
};
use Innmind\BlackBox\Exception\LogicException;
use Innmind\Immutable\SetInterface;
use PHPUnit\Framework\TestCase;

class SetsTest extends TestCase
{
    public function testIntegers()
    {
        $set = integers();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(100, $set);

        $this->assertNotSame($set, iterator_to_array(integers()));
    }

    public function testThrowWhenIntegersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integers(0)->next();
    }

    public function testIntegersExceptZero()
    {
        $set = integersExceptZero();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(100, $set);
        $this->assertFalse(in_array(0, $set, true));

        $this->assertNotSame($set, iterator_to_array(integersExceptZero()));
    }

    public function testThrowWhenIntegersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        integersExceptZero(0)->next();
    }

    public function testNaturalNumbers()
    {
        $set = naturalNumbers();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(100, $set);
        $this->assertTrue(min($set) >= 0);

        $this->assertNotSame($set, iterator_to_array(naturalNumbers()));
    }

    public function testThrowWhenNaturalNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbers(0)->next();
    }

    public function testNaturalNumbersExceptZero()
    {
        $set = naturalNumbersExceptZero();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(100, $set);
        $this->assertTrue(min($set) >= 1);

        $this->assertNotSame($set, iterator_to_array(naturalNumbersExceptZero()));
    }

    public function testThrowWhenNaturalNumbersExceptZeroRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        naturalNumbersExceptZero(0)->next();
    }

    public function testRealNumbers()
    {
        $set = realNumbers();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(100, $set);
        $this->assertTrue(min($set) < 0);

        $this->assertNotSame($set, iterator_to_array(realNumbers()));
    }

    public function testThrowWhenRealNumbersRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        realNumbers(0)->next();
    }

    public function testRange()
    {
        $set = range(-100, 99, 2);

        $this->assertInstanceOf(\Generator::class, $set);
        $this->assertCount(100, $set);
    }

    public function testChar()
    {
        $set = chars();

        $this->assertInstanceOf(\Generator::class, $set);
        $set = iterator_to_array($set);
        $this->assertCount(256, $set);

        $this->assertSame($set, iterator_to_array(chars()));
    }

    public function testStrings()
    {
        $set = strings();

        $this->assertInstanceOf(\Generator::class, $set);
        $this->assertCount(100, $set);
    }

    public function testThrowWhenStringsRangeLessThanOne()
    {
        $this->expectException(LogicException::class);

        strings(0)->next();
    }

    public function testUnsafeStrings()
    {
        $set = unsafeStrings();

        $this->assertInstanceOf(\Generator::class, $set);
    }

    public function testMixed()
    {
        $set = mixed();

        $this->assertInstanceOf(\Generator::class, $set);
    }
}
