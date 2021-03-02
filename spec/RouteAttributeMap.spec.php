<?php

declare(strict_types=1);

use Quanta\Http\RouteAttributeMap;

describe('RouteAttributeMap', function () {

    context('when the there is no matched route attribute', function () {

        beforeEach(function () {
            $this->map = new RouteAttributeMap;
        });

        describe('->all()', function () {

            it('should return an empty array', function () {
                $test = $this->map->all();

                expect($test)->toEqual([]);
            });

        });

        describe('->get()', function () {

            context('when no default value is given', function () {

                it('should return null', function () {
                    $test = $this->map->get('key');

                    expect($test)->toBeNull();
                });

            });

            context('when a default value is given', function () {

                it('should return the given default value', function () {
                    $test = $this->map->get('key', 'default');

                    expect($test)->toEqual('default');
                });

            });

        });

    });

    context('when the there is at least one matched route attribute', function () {

        beforeEach(function () {
            $this->map = new RouteAttributeMap(['id1' => 'value1', 'id2' => 'value2', 'id3' => 'value3']);
        });

        describe('->all()', function () {

            it('should return the matched route attribute map', function () {
                $test = $this->map->all();

                expect($test)->toEqual(['id1' => 'value1', 'id2' => 'value2', 'id3' => 'value3']);
            });

        });

        describe('->get()', function () {

            context('when the given key is in the matched route attribute map', function () {

                context('when no default value is given', function () {

                    it('should return the associated value', function () {
                        $test = $this->map->get('id2');

                        expect($test)->toEqual('value2');
                    });

                });

                context('when a default value is given', function () {

                    it('should return the associated value', function () {
                        $test = $this->map->get('id2');

                        expect($test)->toEqual('value2');
                    });

                });

            });

            context('when the given key is not in the matched route attribute map', function () {

                context('when no default value is given', function () {

                    it('should return null', function () {
                        $test = $this->map->get('id4');

                        expect($test)->toBeNull();
                    });

                });

                context('when a default value is given', function () {

                    it('should return the given default value', function () {
                        $test = $this->map->get('id4', 'default');

                        expect($test)->toEqual('default');
                    });

                });

            });

        });

    });

});
