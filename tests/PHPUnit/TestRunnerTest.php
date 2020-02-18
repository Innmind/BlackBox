<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\TestRunner,
    Set\Integers,
    Set\Decorate,
    Set\Value,
};
use PHPUnit\Framework\{
    TestCase,
    ExpectationFailedException,
};

class TestRunnerTest extends TestCase
{
    public function testRunTheTest()
    {
        $run = new TestRunner;

        $this->assertNull($run(
            fn($a, $b) => $this->assertSame([24, 42], [$a, $b]),
            Value::immutable([24, 42]),
        ));
    }

    public function testShrinkUpToFindSmallestPossibleFailingValue()
    {
        $run = new TestRunner;
        $smallest = null;
        $set = Decorate::immutable(
            fn($i) => [$i],
            Integers::any(),
        );

        try {
            $run(
                function(int $value) use (&$smallest) {
                    $smallest = $value;

                    $this->assertTrue(false); // to trigger shrinking
                },
                $set->values()->current(),
            );
            $this->fail('it should have thrown an exception');
        } catch (ExpectationFailedException $e) {
            $this->assertSame(
                'Failed asserting that false is true.',
                $e->getMessage(),
            );
            $this->assertSame(0, $smallest);
        }
    }

    public function testShrinkingCanBeDisabled()
    {
        $run = new TestRunner(true);
        $set = Decorate::immutable(
            fn($i) => [$i],
            Integers::any(),
        );
        $runned = 0;

        try {
            $run(
                function() use (&$runned) {
                    ++$runned;
                    $this->assertTrue(false);
                },
                $set->values()->current(),
            );
            $this->fail('it should have thrown an exception');
        } catch (ExpectationFailedException $e) {
            $this->assertSame(1, $runned);
        }
    }
}
