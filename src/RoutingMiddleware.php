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
        try {
            $result = $this->router->dispatch($request);
        }

        catch (\Throwable $e) {
            throw new \Exception('Error while dispatching the request', 0, $e);
        }

        $request = $result->request($request);

        return $handler->handle($request);
    }
}
