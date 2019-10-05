<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

interface Set
{
    public function take(int $size): self;
    public function filter(callable $predicate): self;

    /**
     * @param mixed $carry
     *
     * @return mixed
     */
    public function reduce($carry, callable $reducer);
    public function values(): \Generator;
}
