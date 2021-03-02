<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RoutingResult
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface|null
     */
    private ?ServerRequestInterface $mock;

    /**
     * @var \Psr\Http\Server\RequestHandlerInterface|null
     */
    private ?RequestHandlerInterface $handler;

    /**
     * @var array<string, mixed>
     */
    private array $attributes;

    /**
     * @var array<int, string>
     */
    private array $allowed;

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface  $handler
     * @param array<string, mixed>                      $attributes
     * @return \Quanta\Http\RoutingResult
     */
    public static function found(RequestHandlerInterface $handler, array $attributes = []): self
    {
        return new self(null, $handler, $attributes);
    }

    /**
     * @return \Quanta\Http\RoutingResult
     */
    public static function notFound(): self
    {
        return new self;
    }

    /**
     * @param string ...$allowed
     * @return \Quanta\Http\RoutingResult
     */
    public static function notAllowed(string ...$allowed): self
    {
        return new self(null, null, [], $allowed);
    }

    public static function mock(ServerRequestInterface $mock): self
    {
        return new self($mock);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface|null $mock
     * @param \Psr\Http\Server\RequestHandlerInterface|null $handler
     * @param array<string, mixed>                          $attributes
     * @param array<int, string>                            $allowed
     */
    private function __construct(
        ServerRequestInterface $mock = null,
        RequestHandlerInterface $handler = null,
        array $attributes = [],
        array $allowed = []
    ) {
        $this->mock = $mock;
        $this->handler = $handler;
        $this->attributes = $attributes;
        $this->allowed = $allowed;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function request(ServerRequestInterface $request): ServerRequestInterface
    {
        if (!is_null($this->mock)) {
            return $this->mock;
        }

        return is_null($this->handler)
            ? $this->failure($request)
            : $this->success($request, $this->handler);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function failure(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withAttribute(RoutingFailure::class, new RoutingFailure(...$this->allowed));
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    private function success(ServerRequestInterface $request, RequestHandlerInterface $handler): ServerRequestInterface
    {
        $request = $request->withAttribute(RequestHandlerInterface::class, $handler);
        $request = $request->withAttribute(RouteAttributeMap::class, new RouteAttributeMap($this->attributes));

        foreach ($this->attributes as $name => $attribute) {
            $request = $request->withAttribute($name, $attribute);
        }

        return $request;
    }
}
