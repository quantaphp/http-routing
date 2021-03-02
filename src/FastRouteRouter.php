<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use FastRoute\Dispatcher;

final class FastRouteRouter implements RouterInterface
{
    /**
     * @var \FastRoute\Dispatcher
     */
    private $dispatcher;

    /**
     * @param \FastRoute\Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Quanta\Http\RoutingResult
     * @throws \UnexpectedValueException
     */
    public function dispatch(ServerRequestInterface $request): RoutingResult
    {
        $result = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath(),
        );

        if ($result[0] == Dispatcher::NOT_FOUND) {
            return RoutingResult::notFound();
        }

        if ($result[0] == Dispatcher::METHOD_NOT_ALLOWED) {
            return RoutingResult::notAllowed(...$result[1]);
        }

        if ($result[1] instanceof RequestHandlerInterface) {
            return RoutingResult::found($result[1], $result[2]);
        }

        throw new \UnexpectedValueException(
            sprintf('The value matched by the FastRoute Dispatcher must implement %s', RequestHandlerInterface::class),
        );
    }
}
