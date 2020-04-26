<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Random;

use Innmind\BlackBox\Random;

final class MtRand implements Random
{
    private ?int $seed = null;

    public static function seed(int $seed): self
    {
        $self = new self;
        $self->seed = $seed;

        return $self;
    }

    public function __invoke(int $min, int $max): int
    {
        if (\is_int($this->seed)) {
            \mt_srand($this->seed);
        }

        $value = \mt_rand($min, $max);
        \mt_rand();

        return $value;
    }
}
