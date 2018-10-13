<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

use Innmind\BlackBox\{
    Given\InitialValue\Name,
    Exception\LogicException,
};
use Innmind\Immutable\{
    SetInterface,
    StreamInterface,
    Stream,
};

final class Any implements InitialValue
{
    private $name;
    private $set;
    private $dependency;

    public function __construct(Name $name, SetInterface $set)
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
    public function sets(): StreamInterface
    {
        if (!$this->dependency instanceof InitialValue) {
            return $this
                ->set
                ->reduce(
                    Stream::of(SoFar::class),
                    function(StreamInterface $sets, $value): StreamInterface {
                        return $sets->add(
                            (new SoFar)->add($this->name, $value)
                        );
                    }
                );
        }

        $dependency = $this->dependency->sets();

        return $this
            ->set
            ->reduce(
                Stream::of(SoFar::class),
                function(StreamInterface $sets, $value) use ($dependency): StreamInterface {
                    return $sets->append(
                        $dependency->map(function(SoFar $soFar) use ($value): SoFar {
                            return $soFar->add($this->name, $value);
                        })
                    );
                }
            );
    }
}
