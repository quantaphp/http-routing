<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
            $this->request = mock(ServerRequestInterface::class);
            $this->handler = mock(RequestHandlerInterface::class);
        });

        context('when the result is found', function () {

            it('should return the response produced by the routing result', function () {

                $request1 = mock(ServerRequestInterface::class);
                $request2 = mock(ServerRequestInterface::class);
                $request3 = mock(ServerRequestInterface::class);
                $request4 = mock(ServerRequestInterface::class);
                $response = mock(ResponseInterface::class);
                $handler = mock(RequestHandlerInterface::class);

                $result = RoutingResult::found($handler->get(), [
                    'id1' => 'value1',
                    'id2' => 'value2',
                    'id3' => 'value3',
                ]);

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

                $this->router->dispatch->with($this->request)->returns($result);

                $handler->handle->with($request4)->returns($response);

                $test = $this->middleware->process($this->request->get(), $this->handler->get());

                expect($test)->toBe($response->get());

            });

        });

        context('when the result is not found', function () {

            it('should return the response produced by the routing result with the given request handler', function () {

                $request = mock(ServerRequestInterface::class);
                $response = mock(ResponseInterface::class);

                $result = RoutingResult::notFound();

                $this->request->withAttribute
                    ->with(RoutingFailure::class, new RoutingFailure)
                    ->returns($request);

                $this->router->dispatch->with($this->request)->returns($result);

                $this->handler->handle->with($request)->returns($response);

                $test = $this->middleware->process($this->request->get(), $this->handler->get());

                expect($test)->toBe($response->get());

            });

        });

    });

});
