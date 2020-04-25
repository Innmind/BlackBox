<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Properties,
    Property,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};

class PropertiesTest extends TestCase
{
    use BlackBox;

    public function testPropertiesAreAccessible()
    {
        $a = $this->createMock(Property::class);
        $b = $this->createMock(Property::class);

        $this
            ->forAll(Set\Sequence::of(
                Set\Elements::of($a, $b),
                Set\Integers::between(1, 100),
            ))
            ->then(function($list) {
                $properties = new Properties(...$list);

                $this->assertSame($list, $properties->properties());
            });
    }

    public function testNonApplicablePropertiesAreNotApplied()
    {
        $systemUnderTest = new \stdClass;
        $nonApplicable = $this->createMock(Property::class);
        $nonApplicable
            ->expects($this->atLeast(1))
            ->method('applicableTo')
            ->with($this->callback(fn($systemUnderTest) => $systemUnderTest instanceof \stdClass))
            ->WillReturn(false);
        $nonApplicable
            ->expects($this->never())
            ->method('ensureHeldBy');
        $applicable = $this->createMock(Property::class);
        $applicable
            ->expects($this->atLeast(1))
            ->method('applicableTo')
            ->with($this->callback(fn($systemUnderTest) => $systemUnderTest instanceof \stdClass))
            ->WillReturn(true);
        $applicable
            ->expects($this->atLeast(1))
            ->method('ensureHeldBy')
            ->with($this->callback(fn($systemUnderTest) => $systemUnderTest instanceof \stdClass))
            ->willReturn($expected = clone $systemUnderTest);

        $this
            ->forAll(Set\Sequence::of(
                Set\Elements::of($nonApplicable, $applicable),
                Set\Integers::between(1, 100),
            ))
            ->filter(fn($list) => \in_array($applicable, $list, true))
            ->then(function($list) use ($systemUnderTest, $expected) {
                $properties = new Properties(...$list);

                $this->assertSame($expected, $properties->ensureHeldBy($systemUnderTest));
            });
    }
}
