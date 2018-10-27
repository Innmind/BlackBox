<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    Suite,
    Loader,
    Runner,
    Test,
    Test\Report,
    Test\Name,
};
use Innmind\Url\Path;
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
            ->willReturn((function() use ($test) {
                yield $test;
            })());
        $run
            ->expects($this->once())
            ->method('__invoke')
            ->with($test)
            ->willReturn($expected = new Report(new Name('foo')));

        $generator = $suite($path);

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertSame([$expected], iterator_to_array($generator));
    }
}