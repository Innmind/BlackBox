<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Random;

/**
 * Bear in mind this is not a formal proof (in the mathematical sense) but an
 * attempt to prove a property
 */
final class Proof
{
    private string $name;
    private Given $given;
    private When $when;
    private Then $then;

    public function __construct(
        string $name,
        Given $given,
        When $when,
        Then $then
    ) {
        $this->name = $name;
        $this->given = $given;
        $this->when = $when;
        $this->then = $then;
    }

    /**
     * @param callable(): void $pass
     * @param callable(string): void $fail
     */
    public function __invoke(int $tests, Random $rand, callable $pass, callable $fail): void
    {
        /** @psalm-suppress MissingClosureParamType */
        ($this->given)($tests, $rand, fn(...$args) => ($this->then)(
            $pass,
            $fail,
            ($this->when)(...$args),
            ...$args,
        ));
    }
}
