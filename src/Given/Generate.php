<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\LogicException,
};

final class Generate implements InitialValue
{
    private $name;
    private $generate;
    private $dependency;

    public function __construct(Name $name, callable $generate)
    {
        $this->name = $name;
        $this->generate = $generate;
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
            $soFar = new SoFar;

            yield $soFar->add(
                $this->name,
                ($this->generate)($soFar)
            );

            return;
        }

        $dependencySets = $this->dependency->sets();

        foreach ($dependencySets as $soFar) {
            yield $soFar->add(
                $this->name,
                ($this->generate)($soFar)
            );
        }
    }
}
