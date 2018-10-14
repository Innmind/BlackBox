<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Test;

use Innmind\BlackBox\{
    Test\Test,
    Test as TestInterface,
    Test\Name,
    Test\Report,
    Given,
    When,
    Then,
    Assert,
};
use PHPUnit\Framework\TestCase;

class TestTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            TestInterface::class,
            new Test(
                new Name('foo'),
                new Given,
                new When(function(){}),
                new Then
            )
        );
    }

    public function testInvokationSuccess()
    {
        $test = new Test(
            $name = new Name('foo'),
            new Given,
            new When(function(){
                return 42;
            }),
            new Then(
                Assert\same(42)
            )
        );

        $report = $test();

        $this->assertInstanceOf(Report::class, $report);
        $this->assertSame($name, $report->name());
        $this->assertFalse($report->failed());
        $this->assertSame(1, $report->assertions());
    }
}
