<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\LogicException,
};
use Innmind\Immutable\Map;

final class SoFar
{
    private $values;

    public function __construct()
    {
        $this->values = new Map('string', 'mixed');
    }

    public function add(Name $name, $value): self
    {
        if ($this->values->contains((string) $name)) {
            throw new LogicException("$name already defined");
        }

        $self = clone $this;
        $self->values = $self->values->put((string) $name, $value);

        return $self;
    }

    public function get(string $name)
    {
        return $this->values->get($name);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function scenario(): Scenario
    {
        return new Scenario($this->values);
    }
}
