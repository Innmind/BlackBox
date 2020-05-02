<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Random,
};

final class Seeder
{
    private Random $random;

    public function __construct(Random $random)
    {
        $this->random = $random;
    }

    /**
     * @template T
     *
     * @param Set<T> $set
     *
     * @return T
     */
    public function __invoke(Set $set)
    {
        return $set->values($this->random)->current()->unwrap();
    }
}
