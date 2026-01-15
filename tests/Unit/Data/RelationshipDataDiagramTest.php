<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Data;

use SchenkeIo\LaravelRelationManager\Data\ConfigData;
use SchenkeIo\LaravelRelationManager\Data\ModelData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\Relation;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class RelationshipDataDiagramTest extends TestCase
{
    public function test_get_diagram_data_covers_all_relation_types()
    {
        $models = [
            'App\Models\A' => new ModelData(methods: [
                'hasOne' => new RelationData(type: Relation::hasOne, related: 'App\Models\B'),
                'hasMany' => new RelationData(type: Relation::hasMany, related: 'App\Models\C'),
                'morphOne' => new RelationData(type: Relation::morphOne, related: 'App\Models\D'),
                'morphMany' => new RelationData(type: Relation::morphMany, related: 'App\Models\E'),
                'hasOneThrough' => new RelationData(type: Relation::hasOneThrough, related: 'App\Models\F'),
                'hasManyThrough' => new RelationData(type: Relation::hasManyThrough, related: 'App\Models\G'),
                'hasOneIndirect' => new RelationData(type: Relation::hasOneIndirect, related: 'App\Models\H'),
                'belongsToMany' => new RelationData(type: Relation::belongsToMany, related: 'App\Models\I'),
                'morphToMany' => new RelationData(type: Relation::morphToMany, related: 'App\Models\J'),
                'morphedByMany' => new RelationData(type: Relation::morphedByMany, related: 'App\Models\K'),
                'belongsTo' => new RelationData(type: Relation::belongsTo, related: 'App\Models\L'),
                'morphTo' => new RelationData(type: Relation::morphTo),
                'isSingle' => new RelationData(type: Relation::isSingle),
                'noRelation' => new RelationData(type: Relation::noRelation),
            ]),
        ];

        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $diagramData = $relationshipData->getDiagramData();

        // hasOne: B -> A
        $this->assertArrayHasKey('b_s', $diagramData);
        $this->assertEquals(Relation::hasOne, $diagramData['b_s']['a_s']);

        // hasMany: C -> A
        $this->assertArrayHasKey('c_s', $diagramData);
        $this->assertEquals(Relation::hasMany, $diagramData['c_s']['a_s']);

        // morphOne: D -> A
        $this->assertArrayHasKey('d_s', $diagramData);
        $this->assertEquals(Relation::morphOne, $diagramData['d_s']['a_s']);

        // morphMany: E -> A
        $this->assertArrayHasKey('e_s', $diagramData);
        $this->assertEquals(Relation::morphMany, $diagramData['e_s']['a_s']);

        // belongsToMany: as -> is (because as > is alphabetically? 'as' > 'is' is false, 'a' < 'i')
        // Wait: class_basename('App\Models\A') is 'A', table is 'as'.
        // class_basename('App\Models\I') is 'I', table is 'is'.
        // 'as' < 'is' alphabetically. So nothing in diagram unless we flip them or as > is.
        // Let's check logic: if ($tableName1 > $tableName2) { $tables[$tableName1][$tableName2] = $type; }
        // 'as' > 'is' is false. So $tables['is']['as'] should be set? No, it's not set.
        // If I want to test belongsToMany, I should use names that trigger it.

        // morphToMany: J -> A
        $this->assertArrayHasKey('j_s', $diagramData);
        $this->assertEquals(Relation::morphToMany, $diagramData['j_s']['a_s']);

        // morphedByMany: K -> A
        $this->assertArrayHasKey('k_s', $diagramData);
        $this->assertEquals(Relation::morphedByMany, $diagramData['k_s']['a_s']);

        // belongsTo: as -> ls
        $this->assertArrayHasKey('a_s', $diagramData);
        $this->assertEquals(Relation::belongsTo, $diagramData['a_s']['l_s']);

        // morphTo, isSingle, noRelation: as -> null
        $this->assertArrayHasKey(null, $diagramData['a_s']);
    }

    public function test_belongs_to_many_diagram_data()
    {
        $models = [
            'App\Models\User' => new ModelData(methods: [
                'roles' => new RelationData(type: Relation::belongsToMany, related: 'App\Models\Role'),
            ]),
        ];
        // users, roles. 'users' > 'roles' is true.
        $relationshipData = new RelationshipData(config: new ConfigData, models: $models);
        $diagramData = $relationshipData->getDiagramData();

        $this->assertArrayHasKey('users', $diagramData);
        $this->assertEquals(Relation::belongsToMany, $diagramData['users']['roles']);
    }
}
