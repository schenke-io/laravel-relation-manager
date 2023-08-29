<?php

namespace SchenkeIo\LaravelRelationManager\Define;

class PrimaryModel
{
    use RelationshipsForPrimaryModel;

    public function __construct(public string $model)
    {
    }

    public static function model(string $model): PrimaryModel
    {
        return new PrimaryModel($model);
    }
}
