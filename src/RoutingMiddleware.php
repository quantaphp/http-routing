<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class RoutingMiddleware implements MiddlewareInterface
{
    /**
     * @var \Psr\Http\Message\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $factory;

    /**
     * @var \Quanta\Http\RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var string
     */
    private string $attributes;

    /**
     * @param \Psr\Http\Message\ResponseFactoryInterface    $factory
     * @param \Quanta\Http\RouterInterface                  $router
     * @param string                                        $attributes
     */
    public function __construct(ResponseFactoryInterface $factory, RouterInterface $router, string $attributes = 'route::attributes')
    {
        $this->factory = $factory;
        $this->router = $router;
        $this->attributes = $attributes;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->router->dispatch($request);

        if ($result->isNotFound()) {
            return $this->factory->createResponse(404);
        }

        if ($result->isNotAllowed()) {
            return $this->factory->createResponse(405)->withHeader('Allow', implode(', ', $result->allowed()));
        }

        $request = $request
            ->withAttribute(RequesthandlerInterface::class, $result->handler())
            ->withAttribute($this->attributes, $result->attributes());

        return $handler->handle($request);
    }
}
