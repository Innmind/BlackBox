<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Set\Composite,
};

final class Scenario
{
    private $sets;

    public function __construct(Set $first , Set ...$sets)
    {
        \array_unshift($sets, $first);

        $this->sets = $sets;
    }

    public function then(callable $test): void
    {
        if (\count($this->sets) === 1) {
            \reset($this->sets)->reduce(
                null,
                static function($_, $value) use ($test): void {
                    $test($value);
                }
            );

            return;
        }

        $set = Composite::of(
            function(...$args): array {
                return $args;
            },
            ...$this->sets
        );
        $set->reduce(
            null,
            static function($_, array $values) use ($test): void {
                $test(...$values);
            }
        );
    }
}
