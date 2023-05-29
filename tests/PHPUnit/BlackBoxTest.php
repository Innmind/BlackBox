<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
};
use PHPUnit\Framework\TestCase;

class BlackBoxTest extends TestCase
{
    use BlackBox;

    public function testTrait()
    {
        $class = new class() {
            use BlackBox;

            public function assert(): int
            {
                $called = 0;
                $this
                    ->forAll(Set\Integers::any())
                    ->then(static function() use (&$called) {
                        ++$called;
                    });

                return $called;
            }
        };

        // 200 because it reads the `BLACKBOX_SET_SIZE` env var
        $this->assertSame(200, $class->assert());
    }

    public function testDoesntFailWhenTheExceptionIsExpected()
    {
        $this
            ->forAll(Set\Strings::any(), Set\Integers::above(0))
            ->then(function($message, $code) {
                $exception = new class($message, $code) extends \Exception {
                };

                $this->expectException(\get_class($exception));
                $this->expectExceptionMessage($message);
                $this->expectExceptionCode($code);

                throw $exception;
            });
    }
}
