<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template L
 * @template R
 * @implements Implementation<array{L, R}>
 */
final class Zip implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<L> $left
     * @param Implementation<R> $right
     */
    private function __construct(
        private Implementation $left,
        private Implementation $right,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
        $left  = ($this->left)(
            $random,
            static fn() => true,
        );
        $right  = ($this->right)(
            $random,
            static fn() => true,
        );

        while ($left->valid() && $right->valid()) {
            $a = $left->current();
            $b = $right->current();

            if (\is_null($a) || \is_null($b)) {
                return;
            }

            yield Value::of([$a, $b])
                ->predicatedOn($predicate)
                ->map(static fn($pair) => [
                    $pair[0]->unwrap(),
                    $pair[1]->unwrap(),
                ])
                ->shrinkWith(Zip\Shrinker::instance);

            $left->next();
            $right->next();
        }
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template A
     * @template B
     *
     * @param Implementation<A> $left
     * @param Implementation<B> $right
     *
     * @return self<A, B>
     */
    public static function implementation(
        Implementation $left,
        Implementation $right,
    ): self {
        return new self($left, $right);
    }
}
