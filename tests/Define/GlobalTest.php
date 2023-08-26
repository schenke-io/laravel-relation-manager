<?php

use SchenkeIo\LaravelRelationManager\Define\PrimaryModel;

it('`sayEach` function works', function () {
    $this->assertTrue(function_exists('sayEach'));
    $this->assertInstanceOf(PrimaryModel::class, sayEach('User'));
});
