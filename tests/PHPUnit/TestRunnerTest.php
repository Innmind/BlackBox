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
        $run = new TestRunner(
            fn() => null,
            fn() => false,
        );

        $this->assertNull($run(
            fn($a, $b) => $this->assertSame([24, 42], [$a, $b]),
            Value::immutable([24, 42]),
        ));
    }

    public function testShrinkUpToFindSmallestPossibleFailingValue()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => false,
        );
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
        $run = new TestRunner(
            fn() => null,
            fn() => false,
            true,
        );
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

    public function testThrowDirectlyWhenTheFirstFailingValueIsNotShrinkable()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => false,
        );

        try {
            $run(
                function() {
                    $this->assertTrue(false);
                },
                Value::immutable([0]),
            );
            $this->fail('it should have thrown an exception');
        } catch (ExpectationFailedException $e) {
            $this->assertSame(
                'Failed asserting that false is true.',
                $e->getMessage(),
            );
        }
    }

    public function testExpectedExceptionThrownInTestDoesntTriggerShrinking()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => true,
        );
        $set = Decorate::immutable(
            fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values() as $value) {
            try {
                $run(
                    function($int) {
                        throw new \LogicException((string) $int);
                    },
                    $value,
                );
                $this->fail('it should throw');
            } catch (\LogicException $e) {
                $this->assertSame(
                    (string) $value->unwrap()[0],
                    $e->getMessage(),
                );
            }
        }
    }

    public function testExpectedExceptionThrownInTestDoesntTriggerShrinkingWhenShrinkingDisabled()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => true,
            true,
        );
        $set = Decorate::immutable(
            fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values() as $value) {
            try {
                $run(
                    function($int) {
                        throw new \LogicException((string) $int);
                    },
                    $value,
                );
                $this->fail('it should throw');
            } catch (\LogicException $e) {
                $this->assertSame(
                    (string) $value->unwrap()[0],
                    $e->getMessage(),
                );
            }
        }
    }

    public function testUnexpectedExceptionThrownInTestDoesntTriggerShrinkingWhenShrinkingDisabled()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => false,
            true,
        );
        $set = Decorate::immutable(
            fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values() as $value) {
            try {
                $run(
                    function($int) {
                        throw new \LogicException((string) $int);
                    },
                    $value,
                );
                $this->fail('it should throw');
            } catch (\LogicException $e) {
                $this->assertSame(
                    (string) $value->unwrap()[0],
                    $e->getMessage(),
                );
            }
        }
    }

    public function testUnexpectedExceptionThrownInTestDoesntTriggerShrinkingWhenValueIsNotShrinkable()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => false,
        );

        foreach (Integers::any()->values() as $value) {
            try {
                $run(
                    function($int) {
                        throw new \LogicException((string) $int);
                    },
                    Value::immutable([$value->unwrap()]),
                );
                $this->fail('it should throw');
            } catch (\LogicException $e) {
                $this->assertSame(
                    (string) $value->unwrap(),
                    $e->getMessage(),
                );
            }
        }
    }

    public function testUnexpectedExceptionThrownInTestIsShrunkToSmallestFailingValue()
    {
        $run = new TestRunner(
            fn() => null,
            fn() => false,
        );
        $set = Decorate::immutable(
            fn($int) => [$int],
            Integers::above(0),
        );

        foreach ($set->values() as $value) {
            try {
                $run(
                    function($int) {
                        if ($int > 42) {
                            throw new \LogicException((string) $int);
                        }
                    },
                    $value,
                );
                $this->fail('it should throw');
            } catch (\LogicException $e) {
                $this->assertSame(
                    '43',
                    $e->getMessage(),
                );
            }
        }
    }
}
