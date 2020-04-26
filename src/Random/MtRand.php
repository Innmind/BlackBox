<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Random;

use Innmind\BlackBox\Random;

final class MtRand implements Random
{
    public function __invoke(int $min, int $max): int
    {
        return \mt_rand($min, $max);
    }
}
