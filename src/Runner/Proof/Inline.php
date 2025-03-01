<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
    Runner\Given,
};

final class Inline implements Proof
{
    private Name $name;
    private Given $values;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;
    /** @var list<\UnitEnum> */
    private array $tags;
    /** @var ?positive-int */
    private ?int $scenarii;

    /**
     * @param \Closure(Assert, ...mixed): void $test
     * @param list<\UnitEnum> $tags
     * @param ?positive-int $scenarii
     */
    private function __construct(
        Name $name,
        Given $values,
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
     * @param \Closure(Assert, ...mixed): void $test
     */
    public static function of(
        Name $name,
        Given $values,
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
            Given::of(Set::elements([])),
            $test,
            [],
            1,
        );
    }

    #[\Override]
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\Override]
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

    /**
     * @param positive-int $take
     */
    public function take(int $take): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            $this->tags,
            $take,
        );
    }

    #[\Override]
    public function tags(): array
    {
        return $this->tags;
    }

    #[\Override]
    public function scenarii(int $count): Set
    {
        /** @var Set<Scenario> */
        return Set::randomize($this->values->set())
            ->map(fn(array $args) => Scenario\Inline::of($args, $this->test))
            ->take($this->scenarii ?? $count);
    }
}
