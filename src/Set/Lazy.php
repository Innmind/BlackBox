<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\Immutable\{
    SetInterface,
    Set,
    Str,
    MapInterface,
    StreamInterface,
};

final class Lazy implements SetInterface
{
    private $type;
    private $generate;
    private $set;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $type)
    {
        $this->type = Str::of($type);
        $this->set = Set::of($type);
    }

    public static function of(string $type, callable $generate): self
    {
        $self = new self($type);
        $self->generate = $generate;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): Str
    {
        return $this->type;
    }/**
     * {@inheritdoc}
     */
    public function size(): int
    {
        return $this->set()->size();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return $this->size();
    }

    /**
     * {@inheritdoc}
     */
    public function toPrimitive()
    {
        return $this->set()->toPrimitive();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->set()->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->set()->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->set()->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->set()->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->set()->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function intersect(SetInterface $set): SetInterface
    {
        return $this->set()->intersect($set);
    }

    /**
     * {@inheritdoc}
     */
    public function add($element): SetInterface
    {
        return $this->set()->add($element);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($element): bool
    {
        return $this->set()->contains($element);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($element): SetInterface
    {
        return $this->set()->remove($element);
    }

    /**
     * {@inheritdoc}
     */
    public function diff(SetInterface $set): SetInterface
    {
        return $this->set()->diff($set);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(SetInterface $set): bool
    {
        return $this->set()->equals($set);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): SetInterface
    {
        return $this->set()->filter($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function foreach(callable $function): SetInterface
    {
        return $this->set()->foreach($function);
    }

    /**
     * {@inheritdoc}
     */
    public function groupBy(callable $discriminator): MapInterface
    {
        return $this->set()->groupBy($discriminator);
    }

    /**
     * {@inheritdoc}
     */
    public function map(callable $function): SetInterface
    {
        return $this->set()->map($function);
    }

    /**
     * {@inheritdoc}
     */
    public function partition(callable $predicate): MapInterface
    {
        return $this->set()->partition($predicate);
    }

    /**
     * {@inheritdoc}
     */
    public function join(string $separator): Str
    {
        return $this->set()->join($separator);
    }

    /**
     * {@inheritdoc}
     */
    public function sort(callable $function): StreamInterface
    {
        return $this->set()->sort($function);
    }

    /**
     * {@inheritdoc}
     */
    public function merge(SetInterface $set): SetInterface
    {
        return $this->set()->merge($set);
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        return $this->set()->reduce($carry, $reducer);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): SetInterface
    {
        return $this->set()->clear();
    }

    private function set(): SetInterface
    {
        if (!is_callable($this->generate)) {
            return $this->set;
        }

        $this->set = ($this->generate)();
        $this->generate = null;

        return $this->set;
    }
}
