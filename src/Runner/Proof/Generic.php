<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Assert,
};

final class Generic implements Proof
{
    private Name $name;
    /** @var Set<list<mixed>> */
    private Set $values;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;

    /**
     * @param Set<list<mixed>> $values
     * @param \Closure(Assert, ...mixed): void $test
     */
    private function __construct(
        Name $name,
        Set $values,
        \Closure $test,
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->test = $test;
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
        return new self($name, $values, $test);
    }

    public function name(): Name
    {
        return $this->name;
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
