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

        $this->values = $values->reduce(
            [],
            static function(array $values, string $key, $value): array {
                $values[$key] = $value;

                return $values;
            }
        );
    }

    public function get(string $name)
    {
        return $this->values[$name];
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }
}
