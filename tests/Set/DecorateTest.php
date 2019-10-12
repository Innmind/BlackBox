<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Decorate,
    Set\FromGenerator,
    Set,
};
use PHPUnit\Framework\TestCase;

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
            })
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            $this->set
        );
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
                })
            )
        );
    }

    public function testTake()
    {
        $values = \iterator_to_array($this->set->take(2)->values());

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
            ],
            $values
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
            ],
            \iterator_to_array($values->values())
        );
    }

    public function testReduce()
    {
        $values = \iterator_to_array($this->set->values());

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
                ['gc'],
            ],
            $values
        );
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->values());
        $this->assertCount(3, \iterator_to_array($this->set->values()));
    }
}
