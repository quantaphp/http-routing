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
     * @param string ...$allowed
     */
    public function __construct(string ...$allowed)
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
     * @return string
     */
    public function allowedHeaderValue(string $spacer = ', '): string
    {
        return implode($spacer, $this->allowed);
    }
}
