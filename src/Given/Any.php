<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\LogicException,
};
use Innmind\Immutable\SetInterface;

final class Any implements InitialValue
{
    private $name;
    private $set;
    private $dependency;

    public function __construct(Name $name, \Iterator $set)
    {
        $this->name = $name;
        $this->set = $set;
    }

    public function dependOn(InitialValue $initialValue): InitialValue
    {
        if ($this->dependency instanceof InitialValue) {
            throw new LogicException;
        }

        $self = clone $this;
        $self->dependency = $initialValue;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function sets(): \Generator
    {
        if (!$this->dependency instanceof InitialValue) {
            foreach ($this->set as $value) {
                yield (new SoFar)->add($this->name, $value);
            }

            return;
        }

        $dependencySets = iterator_to_array($this->dependency->sets());

        foreach ($this->set as $value) {
            foreach ($dependencySets as $soFar) {
                yield $soFar->add($this->name, $value);
            }
        }
    }
}
