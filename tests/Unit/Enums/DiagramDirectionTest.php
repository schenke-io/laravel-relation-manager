<?php

use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;

test('it can be created from bool', function () {
    expect(DiagramDirection::fromBool(true))->toBe(DiagramDirection::TD)
        ->and(DiagramDirection::fromBool(false))->toBe(DiagramDirection::LR);
});
