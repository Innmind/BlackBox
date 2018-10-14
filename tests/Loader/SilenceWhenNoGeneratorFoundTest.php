<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader\SilenceWhenNoGeneratorFound,
    Loader,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\Path;
use Innmind\Immutable\Stream;
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
        $inner
            ->expects($this->once())
            ->method('__invoke')
            ->with($path)
            ->willReturn($expected = Stream::of(\Generator::class));

        $this->assertSame($expected, $load($path));
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

        $this->assertTrue($load($path)->equals(Stream::of(\Generator::class)));
    }
}
