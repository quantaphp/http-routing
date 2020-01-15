<?php

declare(strict_types=1);

namespace Quanta\Http;

final class RouteAttributeMap
{
    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * @param string    $name
     * @param mixed     $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }
}
