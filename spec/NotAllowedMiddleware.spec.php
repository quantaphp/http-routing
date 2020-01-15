<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use Quanta\Http\RoutingFailure;
use Quanta\Http\NotAllowedMiddleware;

describe('NotAllowedMiddleware', function () {

    beforeEach(function () {
        $this->factory = mock(ResponseFactoryInterface::class);

        $this->middleware = new NotAllowedMiddleware($this->factory->get());
    });

    it('should implement MiddlewareInterface', function () {

        expect($this->middleware)->toBeAnInstanceOf(MiddlewareInterface::class);

    });

    describe('->process()', function () {

        beforeEach(function () {
            $this->request = mock(ServerRequestInterface::class);
            $this->handler = mock(RequestHandlerInterface::class);
        });

        context('when the request has no RoutingFailure attribute', function () {

            it('should return the response produced by the given request handler', function () {

                $response = mock(ResponseInterface::class);

                $this->handler->handle->with($this->request)->returns($response);

                $test = $this->middleware->process($this->request->get(), $this->handler->get());

                expect($test)->toBe($response->get());

            });

        });

        context('when the request has a RoutingFailure attribute', function () {

            context('when the RoutingFailure has no allowed method', function () {

                it('should return the response produced by the given request handler', function () {

                    $response = mock(ResponseInterface::class);

                    $this->request->getAttribute
                        ->with(RoutingFailure::class)
                        ->returns(new RoutingFailure);

                    $this->handler->handle->with($this->request)->returns($response);

                    $test = $this->middleware->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response->get());

                });

            });

            context('when the RoutingFailure has at least one allowed method', function () {

                it('should return a 405 response with an allowed header', function () {

                    $response1 = mock(ResponseInterface::class);
                    $response2 = mock(ResponseInterface::class);

                    $this->request->getAttribute
                        ->with(RoutingFailure::class)
                        ->returns(new RoutingFailure('GET', 'POST'));

                    $this->factory->createResponse->with(405)->returns($response1);

                    $response1->withHeader->with('Allow', 'GET, POST')->returns($response2);

                    $test = $this->middleware->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response2->get());

                });

            });

        });

    });

});
