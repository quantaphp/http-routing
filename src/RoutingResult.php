<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RoutingResult implements MiddlewareInterface
{
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
        return new self($handler, $attributes);
    }

    /**
     * @return \Quanta\Http\RoutingResult
     */
    public static function notFound(): self
    {
        return new self;
    }

    /**
     * @var string ...$allowed
     * @return \Quanta\Http\RoutingResult
     */
    public static function notAllowed(string ...$allowed): self
    {
        return new self(null, [], $allowed);
    }

    /**
     * @param \Psr\Http\Server\RequestHandlerInterface|null $handler
     * @param array<string, mixed>                          $attributes
     * @param array<int, string>                            $allowed
     */
    private function __construct(RequestHandlerInterface $handler = null, array $attributes = [], array $allowed = [])
    {
        $this->handler = $handler;
        $this->attributes = $attributes;
        $this->allowed = $allowed;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return is_null($this->handler)
            ? $this->failure($request, $handler)
            : $this->success($request, $this->handler);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function failure(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute(RoutingFailure::class, new RoutingFailure($this->allowed));

        return $handler->handle($request);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function success(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute(RouteAttributeMap::class, new RouteAttributeMap($this->attributes));

        foreach ($this->attributes as $name => $attribute) {
            $request = $request->withAttribute($name, $attribute);
        }

        return $handler->handle($request);
    }
}
