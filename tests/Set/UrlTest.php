<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Url,
    Set,
};
use Innmind\Url\Url as Model;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function testOf()
    {
        $urls = Url::of();

        $this->assertInstanceOf(Set::class, $urls);
        $this->assertCount(100, \iterator_to_array($urls->values()));
    }
}
