<?php

namespace SchenkeIo\LaravelRelationshipManager\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has a working database', function () {
    $this->assertDatabaseCount('countries', 0);
});
