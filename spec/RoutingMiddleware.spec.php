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

        beforeEach(function () {
            $this->request = new ServerRequest;
            $this->handler = mock(RequestHandlerInterface::class);
        });

        context('when the router throws an exception', function () {

            it('should wrap an Exception around the thrown exception', function () {
                $exception = new Exception;

                $this->router->dispatch->with($this->request)->throws($exception);

                $test = fn () => $this->middleware->process($this->request, $this->handler->get());

                expect($test)->toThrow(new Exception);

                try {
                    $test();
                }

                catch (\Throwable $e) {
                    expect($e->getPrevious())->toBe($exception);
                }
            });

        });

        context('when the router does not throw an exception', function () {

            it('should return the response produced by the routing result with the given request handler', function () {
                $mock = new ServerRequest;

                $response = new Response;

                $result = RoutingResult::mock($mock);

                $this->router->dispatch->with($this->request)->returns($result);

                $this->handler->handle->with($mock)->returns($response);

                $test = $this->middleware->process($this->request, $this->handler->get());

                expect($test)->toBe($response);
            });

        });

    });

});
