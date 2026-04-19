<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Prove;

use Innmind\BlackBox\{
    Set,
    Set\Collapse,
    Set\Provider,
    Runner\Proof\Name,
    Runner\Given,
};

/**
 * @psalm-immutable
 */
final class Proof
{
    private function __construct(
        private Name $name,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(Name $name): self
    {
        return new self($name);
    }

    /**
     * @no-named-arguments
     */
    public function given(Set|Provider $first, Set|Provider ...$rest): Proof\Given
    {
        $given = Collapse::of($first)->map(static fn(mixed $value) => [$value]);

        if (\count($rest) > 0) {
            /** @var Set<list<mixed>> */
            $given = Set::compose(
                static fn(mixed ...$args) => $args,
                $first,
                ...$rest,
            );
        }

        return Proof\Given::of(
            $this->name,
            Given::of($given->randomize()),
        );
    }
}
