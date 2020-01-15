<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Http\NotFoundMiddleware;

describe('NotFoundMiddleware', function () {

    beforeEach(function () {
        $this->factory = mock(ResponseFactoryInterface::class);

        $this->middleware = new NotFoundMiddleware($this->factory->get());
    });

    it('should implement MiddlewareInterface', function () {

        expect($this->middleware)->toBeAnInstanceOf(MiddlewareInterface::class);

    });

    describe('->process()', function () {

        it('should return a 404 response', function () {

            $request = mock(ServerRequestInterface::class);
            $response = mock(ResponseInterface::class);
            $handler = mock(RequestHandlerInterface::class);

            $this->factory->createResponse->with(404)->returns($response);

            $test = $this->middleware->process($request->get(), $handler->get());

            expect($test)->toBe($response->get());

        });

    });

});
