<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RoutingResult
{
    /**
     * @var int
     */
    private const FOUND = 0;

    /**
     * @var int
     */
    private const NOT_FOUND = 1;

    /**
     * @var int
     */
    private const NOT_ALLOWED = 2;

    /**
     * @var int
     */
    private int $type;

    /**
     * @var \Psr\Http\Server\RequestHandlerInterface|null
     */
    private ?RequestHandlerInterface $handler;

    /**
     * @var array<int, string>
     */
    private array $allowed;

    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface  $handler
     * @param array<string, mixed>                      $attributes
     * @return \Quanta\Http\RoutingResult
     */
    public static function found(RequestHandlerInterface $handler, array $attributes = []): self
    {
        return new self(self::FOUND, $handler, [], $attributes);
    }

    /**
     * @return \Quanta\Http\RoutingResult
     */
    public static function notFound(): self
    {
        return new self(self::NOT_FOUND);
    }

    /**
     * @var string ...$allowed
     * @return \Quanta\Http\RoutingResult
     */
    public static function notAllowed(string ...$allowed): self
    {
        return new self(self::NOT_ALLOWED, null, $allowed);
    }

    /**
     * @param int                                           $type
     * @param \Psr\Http\Server\RequestHandlerInterface|null $handler
     * @param array<int, string>                            $allowed
     * @param array<string, mixed>                          $attributes
     */
    private function __construct(int $type, RequestHandlerInterface $handler = null, array $allowed = [], array $attributes = [])
    {
        $this->type = $type;
        $this->handler = $handler;
        $this->allowed = $allowed;
        $this->attributes = $attributes;
    }

    /**
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->type == self::NOT_FOUND;
    }

    /**
     * @return bool
     */
    public function isNotAllowed(): bool
    {
        return $this->type == self::NOT_ALLOWED;
    }

    /**
     * @return array<int, string>
     */
    public function allowed(): array
    {
        return $this->allowed();
    }

    /**
     * @return \Psr\Http\Server\RequestHandlerInterface
     * @throws \Exception
     */
    public function handler(): RequestHandlerInterface
    {
        if (! is_null($this->handler)) {
            return $this->handler;
        }

        throw new \Exception();
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
}
