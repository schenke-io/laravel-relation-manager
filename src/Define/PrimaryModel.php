<?php

namespace SchenkeIo\LaravelRelationshipManager\Define;

class PrimaryModel
{
    use RelationshipsForPrimaryModel;

    public function __construct(public string $model)
    {
    }
}
