<?php

declare(strict_types=1);

namespace Quanta\Http;

final class RoutingFailure
{
    /**
     * @var array<int, string>
     */
    private array $allowed;

    /**
     * @param array<int, string> $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * @return bool
     */
    public function hasAllowedMethods(): bool
    {
        return count($this->allowed) > 0;
    }

    /**
     * @return array<int, string>
     */
    public function allowedHeader(string $spacer = ', '): string
    {
        return implode($spacer, $this->allowed);
    }
}
