<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class NotFoundMiddleware implements MiddlewareInterface
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

        if (is_null($failure)) {
            return $handler->handle($request);
        }

        if (!$failure instanceof RoutingFailure) {
            throw new \UnexpectedValueException(
                vsprintf('The %s request attribute must be an instance of %s, %s given', [
                    RoutingFailure::class,
                    RoutingFailure::class,
                    gettype($failure),
                ])
            );
        }

        return !$failure->hasAllowedMethods()
            ? $this->factory->createResponse(404)
            : $handler->handle($request);
    }
}
