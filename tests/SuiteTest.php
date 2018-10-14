<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Suite,
    Loader,
    Runner,
    Test,
    Test\Report,
};
use Innmind\Url\Path;
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};
use PHPUnit\Framework\TestCase;

class SuiteTest extends TestCase
{
    public function testInvokation()
    {
        $suite = new Suite(
            $load = $this->createMock(Loader::class),
            $run = $this->createMock(Runner::class)
        );
        $path = new Path('foo');
        $test = $this->createMock(Test::class);
        $load
            ->expects($this->once())
            ->method('__invoke')
            ->with($path)
            ->willReturn(Stream::of(\Generator::class, (function() use ($test) {
                yield $test;
            })()));
        $run
            ->expects($this->once())
            ->method('__invoke')
            ->with($test)
            ->willReturn($expected = new Report);

        $reports = $suite($path);

        $this->assertInstanceOf(StreamInterface::class, $reports);
        $this->assertSame(Report::class, (string) $reports->type());
        $this->assertSame([$expected], $reports->toPrimitive());
    }
}
