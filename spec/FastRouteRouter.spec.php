<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use FastRoute\Dispatcher;

use Quanta\Http\RoutingResult;
use Quanta\Http\RouterInterface;
use Quanta\Http\FastRouteRouter;

describe('FastRouteRouter', function () {

    beforeEach(function () {
        $this->dispatcher = mock(Dispatcher::class);

        $this->router = new FastRouteRouter($this->dispatcher->get());
    });

    it('should implement RouterInterface', function () {

        expect($this->router)->toBeAnInstanceOf(RouterInterface::class);

    });

    describe('->dispatch()', function () {

        beforeEach(function () {
            $uri = mock(UriInterface::class);

            $uri->getPath->returns('test');

            $this->request = mock(ServerRequestInterface::class);

            $this->request->getMethod->returns('GET');
            $this->request->getUri->returns($uri);
        });

        context('when the value returned by the dispatcher is NOT_FOUND', function () {

            it ('should return a not found routing result', function () {
                $this->dispatcher->dispatch
                    ->with('GET', 'test')
                    ->returns([Dispatcher::NOT_FOUND]);

                $test = $this->router->dispatch($this->request->get());

                expect($test)->toEqual(RoutingResult::notFound());
            });

        });

        context('when the value returned by the dispatcher is METHOD_NOT_ALLOWED', function () {

            it ('should return a not found routing result', function () {
                $this->dispatcher->dispatch
                    ->with('GET', 'test')
                    ->returns([Dispatcher::METHOD_NOT_ALLOWED, ['POST', 'PUT']]);

                $test = $this->router->dispatch($this->request->get());

                expect($test)->toEqual(RoutingResult::notAllowed('POST', 'PUT'));
            });

        });

        context('when the value returned by the dispatcher is FOUND', function () {

            context('when the value returned by the dispatcher implements RequestHandlerInterface', function () {

                it ('should return a not found routing result', function () {
                    $handler = mock(RequestHandlerInterface::class);

                    $attributes = ['k1' => 'v1', 'k2' => 'v2'];

                    $this->dispatcher->dispatch
                        ->with('GET', 'test')
                        ->returns([Dispatcher::FOUND, $handler->get(), $attributes]);

                    $test = $this->router->dispatch($this->request->get());

                    expect($test)->toEqual(RoutingResult::found($handler->get(), $attributes));
                });

            });

            context('when the value returned by the dispatcher does not implement RequestHandlerInterface', function () {

                it ('should throw an UnexpectedValueException', function () {
                    $attributes = ['k1' => 'v1', 'k2' => 'v2'];

                    $this->dispatcher->dispatch
                        ->with('GET', 'test')
                        ->returns([Dispatcher::FOUND, 'handler', $attributes]);

                    $test = fn () => $this->router->dispatch($this->request->get());

                    expect($test)->toThrow(new UnexpectedValueException);
                });

            });

        });

    });

});
