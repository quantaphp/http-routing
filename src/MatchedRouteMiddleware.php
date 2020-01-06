<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class MatchedRouteMiddleware implements MiddlewareInterface
{
    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $factory;

    /**
     * @param \Psr\Http\Message\ResponseFactoryInterface $factory
     */
    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $request->getAttribute(RoutingResult::class);

        if (! $result instanceof RoutingResult) {
            throw new \UnexpectedValueException(
                vsprintf('Request attribute \'%s\' must be an implementation of %s, %s given', [
                    RoutingResult::class,
                    RoutingResult::class,
                    gettype($result),
                ])
            );
        }

        if ($result->isNotFound()) {
            return $this->factory->createResponse(404);
        }

        if ($result->isNotAllowed()) {
            return $this->factory->createResponse(405)->withHeader('Allow', implode(', ', $result->allowed()));
        }

        return $result->handle($request);
    }
}
