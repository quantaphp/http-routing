<?php

declare(strict_types=1);

namespace Quanta\Http;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matched = $request->getAttribute(RequestHandlerInterface::class);

        if (is_null($matched)) {
            return $handler->handle($request);
        }

        if ($matched instanceof RequestHandlerInterface) {
            return $matched->handle($request);
        }

        throw new \UnexpectedValueException(
            vsprintf('The %s request attribute must implement %s, %s given', [
                RequestHandlerInterface::class,
                RequestHandlerInterface::class,
                gettype($matched),
            ])
        );
    }
}
