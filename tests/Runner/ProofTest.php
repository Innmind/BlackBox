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
                        $fail();
                    })),
                );

                $count = 0;
                $fail = static function() use (&$count) {
                    $count++;
                };
                $proof($iterations, new RandomInt, static fn() => null, $fail);

                $this->assertSame($iterations, $count);
            });
    }
}
