<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\LogicException,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
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
    public function sets(): StreamInterface
    {
        if (!$this->dependency instanceof InitialValue) {
            $soFar = new SoFar;

            return Stream::of(
                SoFar::class,
                $soFar->add(
                    $this->name,
                    ($this->generate)($soFar)
                )
            );
        }

        return $this
            ->dependency
            ->sets()
            ->map(function(SoFar $soFar): SoFar {
                return $soFar->add(
                    $this->name,
                    ($this->generate)($soFar)
                );
            });
    }
}
