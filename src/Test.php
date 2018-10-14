<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Test\Name,
    Test\Report,
};

interface Test
{
    public function __invoke(): Report;
}
