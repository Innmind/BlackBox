<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

interface Random
{
    public function __invoke(int $min, int $max): int;
}
