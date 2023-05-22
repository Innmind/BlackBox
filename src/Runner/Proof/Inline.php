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
    /** @var ?positive-int */
    private ?int $scenarii;

    /**
     * @param Set<list<mixed>> $values
     * @param \Closure(Assert, ...mixed): void $test
     * @param list<\UnitEnum> $tags
     * @param ?positive-int $scenarii
     */
    private function __construct(
        Name $name,
        Set $values,
        \Closure $test,
        array $tags,
        ?int $scenarii,
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->test = $test;
        $this->tags = $tags;
        $this->scenarii = $scenarii;
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
        return new self($name, $values, $test, [], null);
    }

    /**
     * @param \Closure(Assert): void $test
     */
    public static function test(
        Name $name,
        \Closure $test,
    ): self {
        return new self(
            $name,
            Set\Elements::of([]),
            $test,
            [],
            1,
        );
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
            $this->scenarii,
        );
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function scenarii(int $count): Set
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         * @var Set<Scenario>
         */
        return Set\Decorate::immutable(
            fn(array $args) => Scenario\Generic::of($args, $this->test),
            $this->values,
        )->take($this->scenarii ?? $count);
    }
}
