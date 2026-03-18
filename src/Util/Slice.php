<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Util;

final class Slice
{
    /** @var int<0, max> */
    private int $offset;
    /** @var int<0, max> */
    private int $length;
    /** @var int<0, max> */
    private int $minimum;
    private bool $takeLeading;

    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     * @param int<0, max> $minimum
     */
    private function __construct(
        int $offset,
        int $length,
        int $minimum,
        bool $takeLeading,
    ) {
        $this->offset = $offset;
        $this->length = $length;
        $this->minimum = $minimum;
        $this->takeLeading = $takeLeading;
    }

    #[\NoDiscard]
    public function __invoke(array $values): array
    {
        $subset = \array_slice($values, $this->offset, $this->length);

        if ($this->minimum === 0) {
            return $subset;
        }

        if (\count($subset) > $this->minimum) {
            return $subset;
        }

        return match ($this->takeLeading) {
            true => \array_slice($values, 0, $this->minimum),
            false => \array_slice($values, -$this->minimum),
        };
    }

    /**
     * @param int<0, max> $offset
     * @param int<0, max> $length
     * @param int<0, max> $minimum
     */
    #[\NoDiscard]
    public static function of(
        int $offset,
        int $length,
        int $minimum,
        bool $takeLeading,
    ): self {
        return new self($offset, $length, $minimum, $takeLeading);
    }
}
