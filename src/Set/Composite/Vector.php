<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set;

final class Vector
{
    private $values;

    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public static function of(Set $set): self
    {
        return new self(...$set->reduce(
            [],
            static function(array $values, $value): array {
                $values[] = $value;

                return $values;
            }
        ));
    }

    public function dot(self $vector): Matrix
    {
        $combinations = [];

        foreach ($this->values as $a) {
            foreach ($vector->values as $key => $b) {
                $combinations[] = new Combination($a, $b);
            }
        }

        return new Matrix(...$combinations);
    }
}
