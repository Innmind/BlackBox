<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Decorate,
    Set\FromGenerator,
    Set,
    Set\Value,
};

class DecorateTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = new Decorate(
            function(string $value) {
                return [$value];
            },
            FromGenerator::of(function() {
                yield 'ea';
                yield 'fb';
                yield 'gc';
                yield 'eb';
            }),
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, $this->set);
    }

    public function testOf()
    {
        $this->assertInstanceOf(
            Decorate::class,
            Decorate::of(
                function() {},
                FromGenerator::of(function() {
                    yield 'e';
                    yield 'f';
                }),
            ),
        );
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(2)->values());

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
            ],
            $values,
        );
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(string $value): bool {
                return $value[0][0] === 'e';
            });

        $this->assertSame(
            [
                ['ea'],
                ['eb'],
            ],
            $this->unwrap($values->values()),
        );
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->values());

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
                ['gc'],
                ['eb'],
            ],
            $values,
        );
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->values());
        $this->assertCount(4, $this->unwrap($this->set->values()));

        foreach ($this->set->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }
}
