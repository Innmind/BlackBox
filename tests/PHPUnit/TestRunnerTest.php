<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\TestRunner,
    Set\Integers,
    Set\Decorate,
    Set\Value,
    Random\MtRand,
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
            static fn() => null,
            static fn() => false,
        );

        $this->assertNull($run(
            fn($a, $b) => $this->assertSame([24, 42], [$a, $b]),
            Value::immutable([24, 42]),
        ));
    }

    public function testShrinkUpToFindSmallestPossibleFailingValue()
    {
        $run = new TestRunner(
            static fn() => null,
            static fn() => false,
        );
        $smallest = null;
        $set = Decorate::immutable(
            static fn($i) => [$i],
            Integers::any(),
        );

        try {
            $run(
                function(int $value) use (&$smallest) {
                    $smallest = $value;

                    $this->assertTrue(false); // to trigger shrinking
                },
                $set->values(new MtRand)->current(),
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
            static fn() => null,
            static fn() => false,
            true,
        );
        $set = Decorate::immutable(
            static fn($i) => [$i],
            Integers::any(),
        );
        $runned = 0;

        try {
            $run(
                function() use (&$runned) {
                    ++$runned;
                    $this->assertTrue(false);
                },
                $set->values(new MtRand)->current(),
            );
            $this->fail('it should have thrown an exception');
        } catch (ExpectationFailedException $e) {
            $this->assertSame(1, $runned);
        }
    }

    public function testThrowDirectlyWhenTheFirstFailingValueIsNotShrinkable()
    {
        $run = new TestRunner(
            static fn() => null,
            static fn() => false,
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
            static fn() => null,
            static fn() => true,
        );
        $set = Decorate::immutable(
            static fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values(new MtRand) as $value) {
            try {
                $run(
                    static function($int) {
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
            static fn() => null,
            static fn() => true,
            true,
        );
        $set = Decorate::immutable(
            static fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values(new MtRand) as $value) {
            try {
                $run(
                    static function($int) {
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
            static fn() => null,
            static fn() => false,
            true,
        );
        $set = Decorate::immutable(
            static fn($int) => [$int],
            Integers::any(),
        );

        foreach ($set->values(new MtRand) as $value) {
            try {
                $run(
                    static function($int) {
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
            static fn() => null,
            static fn() => false,
        );

        foreach (Integers::any()->values(new MtRand) as $value) {
            try {
                $run(
                    static function($int) {
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
            static fn() => null,
            static fn() => false,
        );
        $set = Decorate::immutable(
            static fn($int) => [$int],
            Integers::above(0),
        );

        foreach ($set->values(new MtRand) as $value) {
            try {
                $run(
                    static function($int) {
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
