<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Random;

use Innmind\BlackBox\Random;

final class RandomInt implements Random
{
    public function __invoke(int $min, int $max): int
    {
        return \random_int($min, $max);
    }
}
