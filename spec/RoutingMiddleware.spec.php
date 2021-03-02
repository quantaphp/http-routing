<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

use Quanta\Http\RoutingResult;
use Quanta\Http\RoutingFailure;
use Quanta\Http\RouterInterface;
use Quanta\Http\RouteAttributeMap;
use Quanta\Http\RoutingMiddleware;

describe('RoutingMiddleware', function () {

    beforeEach(function () {
        $this->router = mock(RouterInterface::class);

        $this->middleware = new RoutingMiddleware($this->router->get());
    });

    it('should implement MiddlewareInterface', function () {
        expect($this->middleware)->toBeAnInstanceOf(MiddlewareInterface::class);
    });

    describe('->process()', function () {

        it('should return the response produced by the routing result with the given request handler', function () {
            $request = new ServerRequest;
            $response = new Response;
            $handler = mock(RequestHandlerInterface::class);

            $mock = new ServerRequest;

            $result = RoutingResult::mock($mock);

            $this->router->dispatch->with($request)->returns($result);

            $handler->handle->with($mock)->returns($response);

            $test = $this->middleware->process($request, $handler->get());

            expect($test)->toBe($response);
        });

    });

});
