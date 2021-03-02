<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ResponseFactory;

use Quanta\Http\RoutingFailure;
use Quanta\Http\NotAllowedMiddleware;

describe('NotAllowedMiddleware', function () {

    beforeEach(function () {
        $this->middleware = new NotAllowedMiddleware(new ResponseFactory);
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

        context('when the request has no RoutingFailure attribute', function () {

            it('should return the response produced by the given request handler', function () {
                $this->handler->handle->with($this->request)->returns($this->response);

                $test = $this->middleware->process($this->request, $this->handler->get());

                expect($test)->toBe($this->response);
            });

        });

        context('when the request has a RoutingFailure attribute', function () {

            context('when the RoutingFailure attribute is not an instance of RoutingFailure', function () {

                it('should throw an UnexpectedValueException', function () {
                    $request = $this->request->withAttribute(RoutingFailure::class, new class {});

                    $test = fn () => $this->middleware->process($request, $this->handler->get());

                    expect($test)->toThrow(new UnexpectedValueException);
                });

            });

            context('when the RoutingFailure attribute is an instance of RoutingFailure', function () {

                context('when the RoutingFailure has no allowed method', function () {

                    it('should return the response produced by the given request handler', function () {
                        $request = $this->request->withAttribute(RoutingFailure::class, new RoutingFailure);

                        $this->handler->handle->with($request)->returns($this->response);

                        $test = $this->middleware->process($request, $this->handler->get());

                        expect($test)->toBe($this->response);
                    });

                });

                context('when the RoutingFailure has at least one allowed method', function () {

                    it('should return a 405 response with an allow header', function () {
                        $request = $this->request->withAttribute(RoutingFailure::class, new RoutingFailure('GET', 'POST'));

                        $this->handler->handle->returns($this->response);

                        $test = $this->middleware->process($request, $this->handler->get());

                        expect($test)->not->toBe($this->response);
                        expect($test->getStatusCode())->toEqual(405);
                        expect($test->getHeaderLine('allow'))->toEqual('GET, POST');
                        expect((string) $test->getBody())->toEqual('');
                    });

                });

            });

        });

    });

});
