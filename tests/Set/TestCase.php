<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\Set\Value;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function unwrap(\Generator $values): array
    {
        $values = \iterator_to_array($values);

        return \array_map(fn(Value $value) => $value->unwrap(), $values);
    }
}
