<?php

namespace SchenkeIo\LaravelRelationManager\Define;

class PrimaryModel
{
    use RelationshipsForPrimaryModel;

    public function __construct(public string $model)
    {
    }
}
