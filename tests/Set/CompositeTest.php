<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Composite,
    Set\FromGenerator,
    Set,
};
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = new Composite(
            'foo',
            function(string ...$args) {
                return implode('', $args);
            },
            FromGenerator::of('', function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of('', function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of('', function() {
                yield 'c';
                yield 'd';
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
            Composite::class,
            Composite::of(
                'foo',
                function() {},
                FromGenerator::of('', function() {
                    yield 'e';
                    yield 'f';
                }),
                FromGenerator::of('', function() {
                    yield 'a';
                    yield 'b';
                }),
                FromGenerator::of('', function() {
                    yield 'c';
                    yield 'd';
                })
            )
        );
    }

    public function testName()
    {
        $this->assertSame('foo', $this->set->name());
    }

    public function testTake()
    {
        $values = $this->set->take(2)->reduce(
            [],
            static function(array $values, $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertSame(
            [
                'eac',
                'ead',
            ],
            $values
        );
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(string $value): bool {
                return $value[0] === 'e';
            })
            ->reduce(
                [],
                static function(array $values, $value): array {
                    $values[] = $value;

                    return $values;
                }
            );

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
            ],
            $values
        );
    }

    public function testReduce()
    {
        $values = $this->set->reduce(
            [],
            static function(array $values, $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
                'fac',
                'fad',
                'fbc',
                'fbd',
            ],
            $values
        );
    }
}
