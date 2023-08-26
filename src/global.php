<?php

use SchenkeIo\LaravelRelationManager\Define\PrimaryModel;

if (! function_exists('sayEach')) {
    /**
     * Construct the Primary model from its name
     */
    function sayEach(string $model): PrimaryModel
    {
        return new PrimaryModel($model);
    }
}
