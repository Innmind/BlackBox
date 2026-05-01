<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Framework\Attributes;

/**
 * @immutable
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class Group
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(private string $name)
    {
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }
}
