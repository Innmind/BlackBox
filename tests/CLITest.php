<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use Innmind\BlackBox\{
    CLI,
    Suites,
    Suite,
    Loader,
    Runner,
    Test,
    Given\Scenario,
    When\Result,
    Then\ScenarioReport,
};
use Innmind\CLI\{
    Command,
    Command\Arguments,
    Command\Options,
    Environment,
};
use Innmind\OperatingSystem\OperatingSystem;
use Innmind\TimeContinuum\ElapsedPeriodInterface;
use Innmind\Url\PathInterface;
use Innmind\Immutable\Map;
use PHPUnit\Framework\TestCase;

class CLITest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Command::class,
            new CLI(
                $this->createMock(OperatingSystem::class),
                new Suites(
                    new Suite(
                        $this->createMock(Loader::class),
                        $this->createMock(Runner::class)
                    )
                )
            )
        );
    }

    public function testFailure()
    {
        $command = new CLI(
            $os = $this->createMock(OperatingSystem::class),
            new Suites(
                new Suite(
                    $load = $this->createMock(Loader::class),
                    $run = $this->createMock(Runner::class)
                )
            ),
            $path = $this->createMock(PathInterface::class)
        );
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
            ->with($os, $test)
            ->willReturn(
                (new Test\Report(new Test\Name('foo')))->add(
                    new Scenario(new Map('string', 'mixed')),
                    new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                    (new ScenarioReport)->fail('foo')
                )
            );
        $env = $this->createMock(Environment::class);
        $env
            ->expects($this->once())
            ->method('exit')
            ->with(1);

        $this->assertNull($command(
            $env,
            new Arguments,
            new Options
        ));
    }

    public function testSuccess()
    {
        $command = new CLI(
            $os = $this->createMock(OperatingSystem::class),
            new Suites(
                new Suite(
                    $load = $this->createMock(Loader::class),
                    $run = $this->createMock(Runner::class)
                )
            ),
            $path = $this->createMock(PathInterface::class)
        );
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
            ->with($os, $test)
            ->willReturn(
                (new Test\Report(new Test\Name('foo')))->add(
                    new Scenario(new Map('string', 'mixed')),
                    new Result(null, $this->createMock(ElapsedPeriodInterface::class)),
                    (new ScenarioReport)->success()
                )
            );
        $env = $this->createMock(Environment::class);
        $env
            ->expects($this->never())
            ->method('exit');

        $this->assertNull($command(
            $env,
            new Arguments,
            new Options
        ));
    }
}
