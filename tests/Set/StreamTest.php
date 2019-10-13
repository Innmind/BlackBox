<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Stream,
    Set,
};
use Innmind\Immutable\Stream as Structure;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Stream('string', new Set\Chars)
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(
            Stream::class,
            Stream::of('string', new Set\Chars)
        );
    }

    public function testGenerates100ValuesByDefault()
    {
        $streams = new Stream('string', new Set\Chars);

        $this->assertInstanceOf(\Generator::class, $streams->values());
        $this->assertCount(100, \iterator_to_array($streams->values()));

        foreach ($streams->values() as $stream) {
            $this->assertInstanceOf(Structure::class, $stream);
            $this->assertSame('string', (string) $stream->type());
        }
    }

    public function testGeneratesSequencesOfDifferentSizes()
    {
        $streams = new Stream(
            'string',
            new Set\Chars,
            Set\Integers::of(0, 50)
        );
        $sizes = [];

        foreach ($streams->values() as $stream) {
            $sizes[] = $stream->size();
        }

        $this->assertTrue(\count(\array_unique($sizes)) > 1);
    }

    public function testTake()
    {
        $streams1 = new Stream('string', new Set\Chars);
        $streams2 = $streams1->take(50);

        $this->assertNotSame($streams1, $streams2);
        $this->assertInstanceOf(Stream::class, $streams2);
        $this->assertCount(100, \iterator_to_array($streams1->values()));
        $this->assertCount(50, \iterator_to_array($streams2->values()));
    }

    public function testFilter()
    {
        $streams1 = new Stream('string', new Set\Chars);
        $streams2 = $streams1->filter(static function($stream): bool {
            return $stream->size() % 2 === 0;
        });

        $this->assertNotSame($streams1, $streams2);
        $this->assertInstanceOf(Stream::class, $streams2);

        $values1 = \iterator_to_array($streams1->values());
        $values2 = \iterator_to_array($streams2->values());
        $values1 = \array_map(function($stream) {
            return $stream->size() % 2;
        }, $values1);
        $values2 = \array_map(function($stream) {
            return $stream->size() % 2;
        }, $values2);
        $values1 = \array_unique($values1);
        $values2 = \array_unique($values2);
        \sort($values1);

        $this->assertSame([0, 1], \array_values($values1));
        $this->assertSame([0], \array_values($values2));
    }
}
