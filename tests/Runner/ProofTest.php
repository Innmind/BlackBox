<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\Proof,
    Runner\Given,
    Runner\When,
    Runner\Then,
    Runner\Hold,
    PHPUnit\BlackBox,
    Set,
    Random\RandomInt,
    Exception\Failure,
};
use PHPUnit\Framework\TestCase;

class ProofTest extends TestCase
{
    use BlackBox;

    public function testRunTheNumberOfGivenTestIterations()
    {
        $this
            ->forAll(Set\Integers::between(1, 10))
            ->then(function($iterations) {
                $proof = new Proof(
                    'run the number of given test iterations',
                    new Given(Set\Strings::any()),
                    new When(static fn($string) => $string),
                    new Then(new Hold(function($held, $fail, $result, $string) {
                        $this->assertFalse($result->thrown());
                        $this->assertSame($string, $result->value());
                        $held();
                    })),
                );

                $count = 0;
                $held = static function() use (&$count) {
                    $count++;
                };
                $proof(
                    $iterations,
                    new RandomInt,
                    static fn() => null,
                    $held,
                    static fn() => null,
                );

                $this->assertSame($iterations, $count);
            });
    }

    public function testCallsThePassCallbackWhenATestIsSuccesful()
    {
        $this
            ->forAll(Set\Integers::between(1, 10))
            ->then(function($iterations) {
                $proof = new Proof(
                    'run the number of given test iterations',
                    new Given(Set\Strings::any()),
                    new When(static fn($string) => $string),
                    new Then(new Hold(static function($held, $fail, $result, $string) {})),
                );

                $count = 0;
                $pass = static function() use (&$count) {
                    $count++;
                };
                $proof(
                    $iterations,
                    new RandomInt,
                    $pass,
                    static fn() => null,
                    static fn() => null,
                );

                $this->assertSame($iterations, $count);
            });
    }

    public function testThePassCallbackIsNotCalledWhenThereIsAFailure()
    {
        $this
            ->forAll(Set\Integers::between(1, 10))
            ->then(function($iterations) {
                $proof = new Proof(
                    'run the number of given test iterations',
                    new Given(Set\Strings::any()),
                    new When(static fn($string) => $string),
                    new Then(new Hold(static function($held, $fail, $result, $string) {
                        $fail('watever');
                    })),
                );

                $count = 0;
                $pass = static function() use (&$count) {
                    $count++;
                };
                $proof(
                    $iterations,
                    new RandomInt,
                    $pass,
                    static fn() => null,
                    static fn() => null,
                );

                $this->assertSame(0, $count);
            });
    }

    public function testFailuresAreDetected()
    {
        $this
            ->forAll(Set\Integers::between(1, 10))
            ->then(function($iterations) {
                $proof = new Proof(
                    'failures are detected',
                    new Given(Set\Strings::any()),
                    new When(static function($string) {
                        throw new \Exception($string);
                    }),
                    new Then(new Hold(function($held, $fail, $result, $string) {
                        $this->assertTrue($result->thrown());
                        $this->assertInstanceOf(\Exception::class, $result->value());
                        $this->assertSame($string, $result->value()->getMessage());
                        $fail('watever');
                    })),
                );

                $count = 0;
                $fail = static function() use (&$count) {
                    $count++;
                };
                $proof(
                    $iterations,
                    new RandomInt,
                    static fn() => null,
                    static fn() => null,
                    $fail,
                );

                $this->assertSame(1, $count, 'Test cases should stop once a failure case has been detected');
            });
    }

    public function testFailuresAreContainedToTheProof()
    {
        $this
            ->forAll(Set\Integers::between(1, 10))
            ->then(function($iterations) {
                $proof = new Proof(
                    'failures are detected',
                    new Given(Set\Strings::any()),
                    new When(static function($string) {
                        throw new \Exception($string);
                    }),
                    new Then(new Hold(static function($held, $fail) {
                        $fail('watever');
                    })),
                );

                $failed = false;
                $fail = static function() use (&$failed) {
                    $failed = true;
                };

                try {
                    $this->assertNull($proof(
                        $iterations,
                        new RandomInt,
                        static fn() => null,
                        static fn() => null,
                        $fail,
                    ));
                    $this->assertTrue($failed);
                } catch (Failure $e) {
                    $this->fail('The failure mechanism should not be exposed outside of the proof');
                }
            });
    }

    public function testFailureCallbackContainsTheProofName()
    {
        $this
            ->forAll(
                Set\Strings::any(),
                Set\Strings::any(),
            )
            ->then(function($name, $reason) {
                $proof = new Proof(
                    $name,
                    new Given(Set\Strings::any()),
                    new When(static function($string) {
                        throw new \Exception($string);
                    }),
                    new Then(new Hold(static function($held, $fail) use ($reason) {
                        $fail($reason);
                    })),
                );

                $failed = false;
                $fail = function($a, $b, $arguments) use ($name, $reason, &$failed) {
                    $this->assertSame($name, $a);
                    $this->assertSame($reason, $b);

                    $failed = true;
                };

                $proof(
                    1,
                    new RandomInt,
                    static fn() => null,
                    static fn() => null,
                    $fail,
                );
                $this->assertTrue($failed);
            });
    }

    public function testShrinksToTheSmallestPossibleValue()
    {
        $proof = new Proof(
            'shrink to the smallest possible value',
            new Given(Set\Strings::any()),
            new When(static function($string) {
                throw new \Exception($string);
            }),
            new Then(new Hold(static function($held, $fail, $result, $string) {
                $fail($string);
            })),
        );

        $thrown = false;
        $fail = function($name, $reason, $arguments) use (&$thrown) {
            $this->assertSame('', $reason);
            $thrown = true;
        };
        $proof(
            1,
            new RandomInt,
            static fn() => null,
            static fn() => null,
            $fail,
        );

        $this->assertTrue($thrown);
    }
}
