<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Set\Composite,
    Set\Randomize,
    Random,
};

final class Scenario
{
    private Random $rand;
    private \Closure $recordFailure;
    private \Closure $expectsException;
    /** @var Set<list<mixed>> */
    private Set $set;
    /** @var \Closure(Set<list<mixed>>): Set<list<mixed>> */
    private \Closure $wrap;
    private TestRunner $run;

    /**
     * @param callable(\Throwable, Set\Value, callable): void $recordFailure
     * @param callable(\Throwable): bool $expectsException
     */
    public function __construct(
        Random $rand,
        callable $recordFailure,
        callable $expectsException,
        Set $first,
        Set ...$sets
    ) {
        if (\count($sets) === 0) {
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
                ...$sets,
            );
        }

        $this->rand = $rand;
        $this->recordFailure = \Closure::fromCallable($recordFailure);
        $this->expectsException = \Closure::fromCallable($expectsException);
        $this->set = $set->take(100);
        /** @var \Closure(Set<list<mixed>>): Set<list<mixed>> */
        $this->wrap = \Closure::fromCallable(static fn(Set $set): Set => new Randomize($set));
        $this->run = new TestRunner(
            $recordFailure,
            $expectsException,
        );
    }

    public function take(int $size): self
    {
        $wrap = $this->wrap;
        $self = clone $this;
        $self->set = $this->set->take($size);
        /** @psalm-suppress MixedArgumentTypeCoercion */
        $self->wrap = \Closure::fromCallable(
            static fn(Set $set): Set => $wrap($set)->take($size),
        );

        return $self;
    }

    public function disableShrinking(): self
    {
        $self = clone $this;
        $self->run = new TestRunner(
            $this->recordFailure,
            $this->expectsException,
            true,
        );

        return $self;
    }

    /**
     * @param callable(mixed...): bool $predicate
     */
    public function filter(callable $predicate): self
    {
        $self = clone $this;
        $self->set = $this->set->filter(
            static fn(array $args): bool => $predicate(...$args),
        );

        return $self;
    }

    /**
     * @template R
     *
     * @param callable(mixed...): R $test
     *
     * @return R The last value returned by the test callback (useful to create dependencies between tests)
     */
    public function then(callable $test)
    {
        $set = ($this->wrap)($this->set);
        $return = null;

        /**
         * @var Set\Value<list<mixed>> $values
         */
        foreach ($set->values($this->rand) as $values) {
            /** @var R */
            $return = ($this->run)($test, $values);
        }

        return $return;
    }
}
