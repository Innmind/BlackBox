<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class FromGenerator implements Set
{
    private $name;
    private $size;
    private $generatorFactory;
    private $predicate;
    private $values;

    /**
     * @param callable(): \Generator $generatorFactory
     */
    public function __construct(string $name, callable $generatorFactory)
    {
        if (!$generatorFactory() instanceof \Generator) {
            throw new \TypeError('Argument 2 must be of type callable(): \Generator');
        }

        $this->name = $name;
        $this->size = 100;
        $this->generatorFactory = $generatorFactory;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    /**
     * @param callable(): \Generator $generatorFactory
     */
    public static function of(string $name, callable $generatorFactory): self
    {
        return new self($name, $generatorFactory);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;
        $self->values = null;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        if (\is_null($this->values)) {
            $values = [];
            $generator = ($this->generatorFactory)();
            $iterations = 0;

            while ($iterations < $this->size && $generator->valid()) {
                $value = $generator->current();

                if (($this->predicate)($value)) {
                    $values[] = $value;
                    ++$iterations;
                }

                $generator->next();
            }

            $this->values = $values;
        }

        return \array_reduce($this->values, $reducer, $carry);
    }
}
