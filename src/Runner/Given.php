<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Random,
    Set,
    Exception\Failure,
};

final class Given
{
    /** @var Set<list<mixed>> */
    private Set $set;

    public function __construct(Set $first, Set ...$rest)
    {
        if (\count($rest) === 0) {
            /** @var Set<list<mixed>> */
            $set = Set\Decorate::immutable(
                /** @psalm-suppress MissingClosureParamType */
                static fn($value): array => [$value],
                $first,
            );
        } else {
            /** @var Set<list<mixed>> */
            $set = Set\Composite::immutable(
                /** @psalm-suppress MissingClosureParamType */
                static fn(...$args): array => $args,
                $first,
                ...$rest,
            );
        }

        /** @var Set<list<mixed>> */
        $this->set = new Set\Randomize($set);
    }

    /**
     * @param callable(...mixed): void $prove
     */
    public function __invoke(int $tests, Random $rand, callable $prove): void
    {
        foreach ($this->set->take($tests)->values($rand) as $value) {
            try {
                $prove(...$value->unwrap());
            } catch (Failure $e) {
                // TODO shrink
            }
        }
    }
}
