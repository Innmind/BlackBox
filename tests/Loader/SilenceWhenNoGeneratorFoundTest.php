<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader\SilenceWhenNoGeneratorFound,
    Loader,
    Test,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\Path;
use PHPUnit\Framework\TestCase;

class SilenceWhenNoGeneratorFoundTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Loader::class,
            new SilenceWhenNoGeneratorFound($this->createMock(Loader::class))
        );
    }

    public function testLoad()
    {
        $load = new SilenceWhenNoGeneratorFound(
            $inner = $this->createMock(Loader::class)
        );
        $path = new Path('foo');
        $expected = $this->createMock(Test::class);
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($path)
            ->willReturn((function() use ($expected) {
                yield $expected;
            })());

        $generator = $load($path);

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertSame([$expected], iterator_to_array($generator));
    }

    public function testReturnEmptyStreamWhenNoTestGeneratorFound()
    {
        $load = new SilenceWhenNoGeneratorFound(
            $inner = $this->createMock(Loader::class)
        );
        $path = new Path('foo');
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($path)
            ->will($this->throwException(new NoTestGeneratorFound));

        $generator = $load($path);

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertFalse($generator->valid());
    }
}
