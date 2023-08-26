<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Define;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Data\ModelRelationData;
use SchenkeIo\LaravelRelationManager\Define\PrimaryModel;
use SchenkeIo\LaravelRelationManager\Define\RelationshipsForPrimaryModel;

class RelationshipsForPrimaryModelTest extends TestCase
{
    protected PrimaryModel $primaryModel;

    protected function setUp(): void
    {
        $this->primaryModel = new class extends PrimaryModel
        {
            use RelationshipsForPrimaryModel;

            public function __construct()
            {
                parent::__construct('modelName');
            }
        };
        parent::setUp();
    }

    public function testHasManyThrough()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->hasManyThrough('a'));
    }

    public function testBelongsToMany()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->belongsToMany('a'));
    }

    public function testHasOneThrough()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->hasOneThrough('a'));
    }

    public function testIsSingle()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->isSingle());
    }

    public function testHasOne()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->hasOne('a'));
    }

    public function testHasMany()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->hasMany('a'));
    }

    public function testIsOneToOne()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->isOneToOne('a'));
    }

    public function testIsOneToMany()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->isOneToMany('a'));
    }

    public function testIsManyToMany()
    {
        $this->assertInstanceOf(ModelRelationData::class, $this->primaryModel->isManyToMany('a'));
    }
}
