<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\Immutable\MapInterface;

final class Scenario
{
    private $values;

    public function __construct(MapInterface $values)
    {
        if ((string) $values->keyType() !== 'string') {
            throw new \TypeError('Argument 1 must be of type MapInterface<string, mixed>');
        }

        $this->values = $values;
    }

    public function get(string $name)
    {
        return $this->values->get($name);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }
}
