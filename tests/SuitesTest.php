<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Suites,
    Suite,
    Test,
    Loader,
    Runner,
};
use Innmind\Url\PathInterface;
use Innmind\Immutable\Stream;
use PHPUnit\Framework\TestCase;

class SuitesTest extends TestCase
{
    public function testInvokation()
    {
        $suites = new Suites(
            new Suite(
                $load = $this->createMock(Loader::class),
                $run = $this->createMock(Runner::class)
            )
        );
        $report = $this->createMock(Suites\Report::class);
        $path = $this->createMock(PathInterface::class);
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
            ->willReturn($testReport = new Test\Report(new Test\Name('foo')));
        $report
            ->expects($this->once())
            ->method('add')
            ->with($testReport)
            ->will($this->returnSelf());

        $this->assertSame($report, $suites($report, $path));
    }
}
