<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

interface IO
{
    public function __invoke(string $data): void;
}
