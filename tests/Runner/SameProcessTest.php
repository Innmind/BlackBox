<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\SameProcess,
    Runner,
    Test,
    Test\Report,
};
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
        $test = $this->createMock(Test::class);
        $test
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($expected = new Report);

        $this->assertSame($expected, $run($test));
    }
}
