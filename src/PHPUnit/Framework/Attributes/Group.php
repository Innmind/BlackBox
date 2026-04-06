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
     * @var non-empty-string
     */
    private string $name;

    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }
}
