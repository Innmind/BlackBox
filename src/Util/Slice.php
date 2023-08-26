<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Util;

final class Slice
{
    /** @var 0|positive-int */
    private int $offset;
    /** @var 0|positive-int */
    private int $length;
    /** @var 0|positive-int */
    private int $minimum;
    private bool $takeLeading;

    /**
     * @param 0|positive-int $offset
     * @param 0|positive-int $length
     * @param 0|positive-int $minimum
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
     * @param 0|positive-int $offset
     * @param 0|positive-int $length
     * @param 0|positive-int $minimum
     */
    public static function of(
        int $offset,
        int $length,
        int $minimum,
        bool $takeLeading,
    ): self {
        return new self($offset, $length, $minimum, $takeLeading);
    }
}
