<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationshipDataDiagramTest extends TestCase
{
    public function test_get_diagram_data_covers_all_relation_types()
    {
        $models = [
            'App\Models\A' => new ModelData(methods: [
                'hasOne' => new RelationData(type: EloquentRelation::hasOne, related: 'App\Models\B'),
                'hasMany' => new RelationData(type: EloquentRelation::hasMany, related: 'App\Models\C'),
                'morphOne' => new RelationData(type: EloquentRelation::morphOne, related: 'App\Models\D'),
                'morphMany' => new RelationData(type: EloquentRelation::morphMany, related: 'App\Models\E'),
                'hasOneThrough' => new RelationData(type: EloquentRelation::hasOneThrough, related: 'App\Models\F'),
                'hasManyThrough' => new RelationData(type: EloquentRelation::hasManyThrough, related: 'App\Models\G'),
                'hasOneIndirect' => new RelationData(type: EloquentRelation::hasOneIndirect, related: 'App\Models\H'),
                'belongsToMany' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\I'),
                'morphToMany' => new RelationData(type: EloquentRelation::morphToMany, related: 'App\Models\J'),
                'morphedByMany' => new RelationData(type: EloquentRelation::morphedByMany, related: 'App\Models\K'),
                'belongsTo' => new RelationData(type: EloquentRelation::belongsTo, related: 'App\Models\L'),
                'morphTo' => new RelationData(type: EloquentRelation::morphTo),
                'isSingle' => new RelationData(type: EloquentRelation::isSingle),
                'noRelation' => new RelationData(type: EloquentRelation::noRelation),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $diagramData = $relationshipData->getDiagramData();

        // hasOne: A -> B
        $this->assertArrayHasKey('a_s', $diagramData);
        $this->assertEquals(EloquentRelation::hasOne, $diagramData['a_s']['b_s']);

        // hasMany: A -> C
        $this->assertEquals(EloquentRelation::hasMany, $diagramData['a_s']['c_s']);

        // morphOne: A -> D
        $this->assertEquals(EloquentRelation::morphOne, $diagramData['a_s']['d_s']);

        // morphMany: A -> E
        $this->assertEquals(EloquentRelation::morphMany, $diagramData['a_s']['e_s']);

        // belongsToMany: A -> I
        $this->assertEquals(EloquentRelation::belongsToMany, $diagramData['a_s']['i_s']);

        // morphToMany: A -> J
        $this->assertEquals(EloquentRelation::morphToMany, $diagramData['a_s']['j_s']);

        // morphedByMany: A -> K
        $this->assertEquals(EloquentRelation::morphedByMany, $diagramData['a_s']['k_s']);

        // belongsTo: A -> L
        $this->assertEquals(EloquentRelation::belongsTo, $diagramData['a_s']['l_s']);

        // morphTo, isSingle, noRelation: A -> null
        $this->assertArrayHasKey(null, $diagramData['a_s']);
    }

    public function test_belongs_to_many_diagram_data()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'roles' => new RelationData(type: EloquentRelation::belongsToMany, related: 'App\Models\Role'),
            ]),
        ];
        // users, roles. 'users' > 'roles' is true.
        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $diagramData = $relationshipData->getDiagramData();

        $this->assertArrayHasKey('users', $diagramData);
        $this->assertEquals(EloquentRelation::belongsToMany, $diagramData['users']['roles']);
    }
}
