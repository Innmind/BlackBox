<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Random\RandomInt,
};
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\{
    ResultPrinter,
    DefaultResultPrinter,
};

trait BlackBox
{
    protected function forAll(Set $first, Set ...$sets): Scenario
    {
        $expectsException = static fn(\Throwable $e): bool => false;
        $recordFailure = function(\Throwable $e, Set\Value $values, callable $test): void {
            if (\class_exists(ResultPrinter::class)) {
                ResultPrinterV8::record($this, $e, $values, $test);
            }

            if (\class_exists(DefaultResultPrinter::class)) {
                ResultPrinterV9::record($this, $e, $values, $test);
            }
        };

        if ($this instanceof TestCase) {
            $expectsException = function(\Throwable $e): bool {
                $expectedException = $this->getExpectedException();
                $expectedExceptionMessage = $this->getExpectedExceptionMessage();
                $expectedExceptionCode = $this->getExpectedExceptionCode();

                if (
                    \is_null($expectedException) &&
                    \is_null($expectedExceptionMessage) &&
                    \is_null($expectedExceptionCode)
                ) {
                    return false;
                }

                if (\is_string($expectedException) && !$e instanceof $expectedException) {
                    return false;
                }

                if (\is_string($expectedExceptionMessage) && $expectedExceptionMessage !== $e->getMessage()) {
                    return false;
                }

                if (!\is_null($expectedExceptionCode) && ((string) $expectedExceptionCode !== (string) $e->getCode())) {
                    return false;
                }

                return true;
            };
        }

        $scenario = new Scenario(
            new RandomInt,
            $recordFailure,
            $expectsException,
            $first,
            ...$sets,
        );
        $size = \getenv('BLACKBOX_SET_SIZE');
        $disableShrinking = (bool) \getenv('BLACKBOX_DISABLE_SHRINKING');

        if ($size !== false) {
            $scenario = $scenario->take((int) $size);
        }

        if ($disableShrinking) {
            $scenario = $scenario->disableShrinking();
        }

        return $scenario;
    }

    /**
     * Use the seeder to generate random values to initiate your properties
     */
    protected function seeder(): Seeder
    {
        return new Seeder(new RandomInt);
    }
}
