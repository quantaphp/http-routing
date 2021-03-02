<?php

declare(strict_types=1);

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Server\RequestHandlerInterface;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;

use Quanta\Http\RoutingResult;
use Quanta\Http\RoutingFailure;
use Quanta\Http\RouteAttributeMap;

describe('RoutingResult', function () {

    beforeEach(function () {
        $this->request = new ServerRequest;
    });

    context('when the result is a mock', function () {

        beforeEach(function () {
            $this->mock = new ServerRequest;

            $this->result = RoutingResult::mock($this->mock);
        });

        it('should be an instance of RoutingResult', function () {
            expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
        });

        describe('->request()', function () {

            it('should return the mocked request', function () {
                $test = $this->result->request($this->request);

                expect($test)->toBe($this->mock);
            });

        });

    });

    context('when the result is not found', function () {

        beforeEach(function () {
            $this->result = RoutingResult::notFound();
        });

        it('should be an instance of RoutingResult', function () {
            expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
        });

        describe('->request()', function () {

            it('should add a routing failure attribute to the request', function () {
                $request = $this->request->withAttribute(RoutingFailure::class, new RoutingFailure);

                $test = $this->result->request($this->request);

                expect($test)->toEqual($request);
            });

        });

    });

    context('when the result is not allowed', function () {

        context('when there is no allowed method', function () {

            beforeEach(function () {
                $this->result = RoutingResult::notAllowed();
            });

            it('should be an instance of RoutingResult', function () {
                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
            });

            describe('->request()', function () {

                it('should add a routing failure attribute to the request', function () {
                    $request = $this->request->withAttribute(RoutingFailure::class, new RoutingFailure);

                    $test = $this->result->request($this->request);

                    expect($test)->toEqual($request);
                });

            });

        });

        context('when there is at least one allowed method', function () {

            beforeEach(function () {
                $this->result = RoutingResult::notAllowed('GET', 'POST');
            });

            it('should be an instance of RoutingResult', function () {
                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
            });

            describe('->request()', function () {

                it('should add a routing failure attribute with the allowed methods to the request', function () {
                    $request = $this->request->withAttribute(RoutingFailure::class, new RoutingFailure('GET', 'POST'));

                    $test = $this->result->request($this->request);

                    expect($test)->toEqual($request);
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

            it('should be an instance of RoutingResult', function () {
                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
            });

            describe('->request()', function () {

                it('should add the matched request handler and an empty attribute map to the request', function () {
                    $request = $this->request
                        ->withAttribute(RequestHandlerInterface::class, $this->matched->get())
                        ->withAttribute(RouteAttributeMap::class, new RouteAttributeMap);

                    $test = $this->result->request($this->request);

                    expect($test)->toEqual($request);
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

            it('should be an instance of RoutingResult', function () {
                expect($this->result)->toBeAnInstanceOf(RoutingResult::class);
            });

            describe('->request()', function () {

                it('should add the matched request handler, an attribute map and the attributes to the request', function () {
                    $request = $this->request
                        ->withAttribute(RequestHandlerInterface::class, $this->matched->get())
                        ->withAttribute(RouteAttributeMap::class, new RouteAttributeMap(['id1' => 'value1', 'id2' => 'value2', 'id3' => 'value3']))
                        ->withAttribute('id1', 'value1')
                        ->withAttribute('id2', 'value2')
                        ->withAttribute('id3', 'value3');

                    $test = $this->result->request($this->request);

                    expect($test)->toEqual($request);
                });

            });

        });

    });

});
