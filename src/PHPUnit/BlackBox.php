<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Random\MtRand,
};
use PHPUnit\Framework\TestCase;

trait BlackBox
{
    protected function forAll(Set $first, Set ...$sets): Scenario
    {
        $expectsException = static fn(\Throwable $e): bool => false;
        $recordFailure = function(\Throwable $e, Set\Value $values, callable $test): void {
            ResultPrinterV8::record($this, $e, $values, $test);
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
            new MtRand,
            $recordFailure,
            $expectsException,
            $first,
            ...$sets,
        );
        $size = \getenv('BLACKBOX_SET_SIZE');

        if ($size !== false) {
            $scenario = $scenario->take((int) $size);
        }

        return $scenario;
    }
}
