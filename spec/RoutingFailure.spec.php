<?php

declare(strict_types=1);

use Quanta\Http\RoutingFailure;

describe('RoutingFailure', function () {

    context('when the there is no allowed method', function () {

        beforeEach(function () {
            $this->failure = new RoutingFailure;
        });

        describe('->hasAllowedMethods()', function () {

            it('should be falsy', function () {

                $test = $this->failure->hasAllowedMethods();

                expect($test)->toBeFalsy();

            });

        });

        describe('->allowedHeaderValue()', function () {

            context('when no spacer is given', function () {

                it('should return an empty string', function () {

                    $test = $this->failure->allowedHeaderValue();

                    expect($test)->toEqual('');

                });

            });

            context('when a spacer is given', function () {

                it('should return an empty string', function () {

                    $test = $this->failure->allowedHeaderValue(' - ');

                    expect($test)->toEqual('');

                });

            });

        });

    });

    context('when the there is at least one allowed method', function () {

        beforeEach(function () {
            $this->failure = new RoutingFailure('GET', 'POST');
        });

        describe('->hasAllowedMethods()', function () {

            it('should be truthy', function () {

                $test = $this->failure->hasAllowedMethods();

                expect($test)->toBeTruthy();

            });

        });

        describe('->allowedHeaderValue()', function () {

            context('when no spacer is given', function () {

                it('should use , as a spacer', function () {

                    $test = $this->failure->allowedHeaderValue();

                    expect($test)->toEqual('GET, POST');

                });

            });

            context('when a spacer is given', function () {

                it('should use the given spacer', function () {

                    $test = $this->failure->allowedHeaderValue(' - ');

                    expect($test)->toEqual('GET - POST');

                });

            });

        });

    });

});
