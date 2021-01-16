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
                    new Then(new Hold(function($pass, $fail, $result, $string) {
                        $this->assertFalse($result->thrown());
                        $this->assertSame($string, $result->value());
                        $pass();
                    })),
                );

                $count = 0;
                $pass = static function() use (&$count) {
                    $count++;
                };
                $proof($iterations, new RandomInt, $pass, static fn() => null);

                $this->assertSame($iterations, $count);
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
                    new Then(new Hold(function($pass, $fail, $result, $string) {
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
                $proof($iterations, new RandomInt, static fn() => null, $fail);

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
                    new Then(new Hold(static function($pass, $fail) {
                        $fail('watever');
                    })),
                );

                $fail = static function() {
                    throw new Failure;
                };

                try {
                    $this->assertNull($proof($iterations, new RandomInt, static fn() => null, $fail));
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
                    new Then(new Hold(static function($pass, $fail) use ($reason) {
                        $fail($reason);
                    })),
                );

                $fail = function($a, $b) use ($name, $reason) {
                    $this->assertSame($name, $a);
                    $this->assertSame($reason, $b);

                    throw new Failure;
                };

                $proof(1, new RandomInt, static fn() => null, $fail);
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
            new Then(new Hold(static function($pass, $fail, $result, $string) {
                $fail($string);
            })),
        );

        $thrown = false;
        $fail = function($name, $reason) use (&$thrown) {
            $this->assertSame('', $reason);
            $thrown = true;
        };
        $proof(1, new RandomInt, static fn() => null, $fail);

        $this->assertTrue($thrown);
    }
}
