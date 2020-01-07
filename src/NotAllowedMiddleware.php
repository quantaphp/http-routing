<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class NotAllowedMiddleware implements MiddlewareInterface
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
        $failure = $request->getAttribute(RoutingFailure::class);

        return $failure instanceof RoutingFailure && $failure->hasAllowedMethods()
            ? $this->factory->createResponse(405)->withHeader('Allow', $failure->allowedMethods())
            : $handler->handle($request);
    }
}
