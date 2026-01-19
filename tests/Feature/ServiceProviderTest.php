<?php

use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;

it('can resolve ModelScanner from the container', function () {
    $scanner = app(ModelScanner::class);
    expect($scanner)->toBeInstanceOf(ModelScanner::class);
});
