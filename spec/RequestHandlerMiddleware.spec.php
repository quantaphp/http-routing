<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

use Quanta\Http\RoutingFailure;
use Quanta\Http\RequestHandlerMiddleware;

describe('RequestHandlerMiddleware', function () {

    beforeEach(function () {
        $this->middleware = new RequestHandlerMiddleware;
    });

    it('should implement MiddlewareInterface', function () {
        expect($this->middleware)->toBeAnInstanceOf(MiddlewareInterface::class);
    });

    describe('->process()', function () {

        beforeEach(function () {
            $this->request = new ServerRequest;
            $this->response = new Response;
            $this->handler = mock(RequestHandlerInterface::class);
        });

        context('when the request has no RequestHandlerInterface attribute', function () {

            it('should return the response produced by the given request handler', function () {
                $this->handler->handle->with($this->request)->returns($this->response);

                $test = $this->middleware->process($this->request, $this->handler->get());

                expect($test)->toBe($this->response);
            });

        });

        context('when the request has a RequestHandlerInterface attribute', function () {

            context('when the matched request handler implements RequestHandlerInterface', function () {

                it('should return the response produced by the matched request handler', function () {
                    $handler = mock(RequestHandlerInterface::class);

                    $request = $this->request->withAttribute(RequestHandlerInterface::class, $handler->get());

                    $handler->handle->with($request)->returns($this->response);

                    $test = $this->middleware->process($request, $this->handler->get());

                    expect($test)->toBe($this->response);
                });

            });

            context('when the matched request handler does not implement RequestHandlerInterface', function () {

                it('should throw an UnepectedValueException', function () {
                    $request = $this->request->withAttribute(RequestHandlerInterface::class, new class {});

                    $test = fn () => $this->middleware->process($request, $this->handler->get());

                    expect($test)->toThrow(new UnexpectedValueException);
                });

            });

        });

    });

});
