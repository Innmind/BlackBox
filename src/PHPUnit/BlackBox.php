<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Application,
    Runner\Given,
    PHPUnit\Framework\TestCase,
};

trait BlackBox
{
    protected static function forAll(Set $first, Set ...$rest): Compatibility
    {
        $app = Application::new([]);

        $size = \getenv('BLACKBOX_SET_SIZE');
        $disableShrinking = (bool) \getenv('BLACKBOX_DISABLE_SHRINKING');

        if ($size !== false) {
            $app = $app->scenariiPerProof((int) $size);
        }

        if ($disableShrinking) {
            $app = $app->disableShrinking();
        }

        /** @var Set<list<mixed>> */
        $given = $first->map(static fn(mixed $value) => [$value]);

        if (\count($rest) > 0) {
            /** @var Set<list<mixed>> */
            $given = Set\Composite::immutable(
                static fn(mixed ...$args) => $args,
                $first,
                ...$rest,
            );
        }

        $given = Given::of(Set\Randomize::of($given));

        if (\is_a(self::class, TestCase::class, true)) {
            return Compatibility::blackbox($app, $given);
        }

        return Compatibility::phpunit($app, $given);
    }
}
