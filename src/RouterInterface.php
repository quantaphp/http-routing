<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Quanta\Http\RoutingResult
     */
    public function dispatch(ServerRequestInterface $request): RoutingResult;
}
