<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
};

final class Inline implements Proof
{
    private Name $name;
    /** @var Set<list<mixed>> */
    private Set $values;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param Set<list<mixed>> $values
     * @param \Closure(Assert, ...mixed): void $test
     * @param list<\UnitEnum> $tags
     */
    private function __construct(
        Name $name,
        Set $values,
        \Closure $test,
        array $tags,
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->test = $test;
        $this->tags = $tags;
    }

    /**
     * @param Set<list<mixed>> $values
     * @param \Closure(Assert, ...mixed): void $test
     */
    public static function of(
        Name $name,
        Set $values,
        \Closure $test,
    ): self {
        return new self($name, $values, $test, []);
    }

    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            [...$this->tags, ...$tags],
        );
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function scenarii(): Set
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         * @var Set<Scenario>
         */
        return Set\Decorate::immutable(
            fn(array $args) => Scenario\Generic::of($args, $this->test),
            $this->values,
        );
    }
}
