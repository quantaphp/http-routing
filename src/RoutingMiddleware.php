<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RoutingMiddleware implements MiddlewareInterface
{
    /**
     * @var \Quanta\Http\RouterInterface
     */
    private RouterInterface $router;

    /**
     * @param \Quanta\Http\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->router->dispatch($request);

        $request = $request->withAttribute(RoutingResult::class, $result);

        return $handler->handle($request);
    }
}
