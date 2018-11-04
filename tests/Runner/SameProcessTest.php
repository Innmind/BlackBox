<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\SameProcess,
    Runner,
    Test,
    Test\Report,
    Test\Name,
};
use Innmind\OperatingSystem\OperatingSystem;
use PHPUnit\Framework\TestCase;

class SameProcessTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Runner::class, new SameProcess);
    }

    public function testInvokation()
    {
        $run = new SameProcess;
        $os = $this->createMock(OperatingSystem::class);
        $test = $this->createMock(Test::class);
        $test
            ->expects($this->once())
            ->method('__invoke')
            ->with($os)
            ->willReturn($expected = new Report(new Name('foo')));

        $this->assertSame(
            $expected,
            $run($os, $test)
        );
    }
}
