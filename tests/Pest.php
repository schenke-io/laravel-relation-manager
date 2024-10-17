<?php

use SchenkeIo\LaravelRelationManager\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

if (! class_exists('AllowDynamicProperties')) {
    /*
     * needed in PHP 8.2 but generates warning in PHP 8.1 already
     */
    class AllowDynamicProperties {}
}
