<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Quanta\Http\RoutingResult;
use Quanta\Http\RoutingFailure;
use Quanta\Http\RouteAttributeMap;

describe('RoutingResult', function () {

    beforeEach(function () {
        $this->request = mock(ServerRequestInterface::class);
        $this->handler = mock(RequestHandlerInterface::class);
    });

    context('when the result is not found', function () {

        beforeEach(function () {
            $this->result = RoutingResult::notFound();
        });

        it('should implements MiddlewareInterface', function () {

            expect($this->result)->toBeAnInstanceOf(MiddlewareInterface::class);

        });

        it('should be an instance of RoutingResult', function () {

            expect($this->result)->toBeAnInstanceOf(RoutingResult::class);

        });

        describe('->process()', function () {

            it('should return the response produced by the given request handler', function () {

                $request = mock(ServerRequestInterface::class);
                $response = mock(ResponseInterface::class);

                $failure = new RoutingFailure;

                $this->request->withAttribute
                    ->with(RoutingFailure::class, $failure)
                    ->returns($request);

                $this->handler->handle->with($request)->returns($response);

                $test = $this->result->process($this->request->get(), $this->handler->get());

                expect($test)->toBe($response->get());

            });

        });

    });

    context('when the result is not allowed', function () {

        context('when there is no allowed method', function () {

            beforeEach(function () {
                $this->result = RoutingResult::notAllowed();
            });

            it('should implements MiddlewareInterface', function () {

                expect($this->result)->toBeAnInstanceOf(MiddlewareInterface::class);

            });

            it('should be an instance of RoutingResult', function () {

                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);

            });

            describe('->process()', function () {

                it('should return the response produced by the given request handler', function () {

                    $request = mock(ServerRequestInterface::class);
                    $response = mock(ResponseInterface::class);

                    $failure = new RoutingFailure;

                    $this->request->withAttribute
                        ->with(RoutingFailure::class, $failure)
                        ->returns($request);

                    $this->handler->handle->with($request)->returns($response);

                    $test = $this->result->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response->get());

                });

            });

        });

        context('when there is at least one allowed method', function () {

            beforeEach(function () {
                $this->result = RoutingResult::notAllowed('GET', 'POST');
            });

            it('should implements MiddlewareInterface', function () {

                expect($this->result)->toBeAnInstanceOf(MiddlewareInterface::class);

            });

            it('should be an instance of RoutingResult', function () {

                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);

            });

            describe('->process()', function () {

                it('should return the response produced by the given request handler', function () {

                    $request = mock(ServerRequestInterface::class);
                    $response = mock(ResponseInterface::class);

                    $failure = new RoutingFailure('GET', 'POST');

                    $this->request->withAttribute
                        ->with(RoutingFailure::class, $failure)
                        ->returns($request);

                    $this->handler->handle->with($request)->returns($response);

                    $test = $this->result->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response->get());

                });

            });

        });

    });

    context('when the result is found', function () {

        beforeEach(function () {
            $this->matched = mock(RequestHandlerInterface::class);
        });

        context('when there is no matched route attribute', function () {

            beforeEach(function () {
                $this->result = RoutingResult::found($this->matched->get());
            });

            it('should implements MiddlewareInterface', function () {

                expect($this->result)->toBeAnInstanceOf(MiddlewareInterface::class);

            });

            it('should be an instance of RoutingResult', function () {

                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);

            });

            describe('->process()', function () {

                it('should return the response produced by the matched request handler', function () {

                    $request = mock(ServerRequestInterface::class);
                    $response = mock(ResponseInterface::class);

                    $attributes = new RouteAttributeMap;

                    $this->request->withAttribute
                        ->with(RouteAttributeMap::class, $attributes)
                        ->returns($request);

                    $this->matched->handle->with($request)->returns($response);

                    $test = $this->result->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response->get());

                });

            });

        });

        context('when there is at least one matched route attribute', function () {

            beforeEach(function () {
                $this->result = RoutingResult::found($this->matched->get(), [
                    'id1' => 'value1',
                    'id2' => 'value2',
                    'id3' => 'value3',
                ]);
            });

            it('should implements MiddlewareInterface', function () {

                expect($this->result)->toBeAnInstanceOf(MiddlewareInterface::class);

            });

            it('should be an instance of RoutingResult', function () {

                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);

            });

            describe('->process()', function () {

                it('should return the response produced by the matched request handler', function () {

                    $request1 = mock(ServerRequestInterface::class);
                    $request2 = mock(ServerRequestInterface::class);
                    $request3 = mock(ServerRequestInterface::class);
                    $request4 = mock(ServerRequestInterface::class);
                    $response = mock(ResponseInterface::class);

                    $attributes = new RouteAttributeMap([
                        'id1' => 'value1',
                        'id2' => 'value2',
                        'id3' => 'value3',
                    ]);

                    $this->request->withAttribute
                        ->with(RouteAttributeMap::class, $attributes)
                        ->returns($request1);

                    $request1->withAttribute->with('id1', 'value1')->returns($request2);
                    $request2->withAttribute->with('id2', 'value2')->returns($request3);
                    $request3->withAttribute->with('id3', 'value3')->returns($request4);

                    $this->matched->handle->with($request4)->returns($response);

                    $test = $this->result->process($this->request->get(), $this->handler->get());

                    expect($test)->toBe($response->get());

                });

            });

        });

    });

});
