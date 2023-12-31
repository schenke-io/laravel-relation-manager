<?php

/**
 * ## Test of all models defined
 *
 * ------
 *
 * This file is auto-generated by: SchenkeIo\LaravelRelationManager\Demo\DemoCommand
 * rewrite this test-file on the console with: composer write-file
 */

namespace SchenkeIo\LaravelRelationManager\Tests\Define;

use Illuminate\Foundation\Testing\RefreshDatabase;
use SchenkeIo\LaravelRelationManager\Demo\DemoCommand;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelationships;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class TestProjectTest extends TestCase
{
    use RefreshDatabase;
    use AssertModelRelationships;

    /**
     * @return void
     *
     * Since this class is written by the Command file SchenkeIo\LaravelRelationManager\Demo\DemoCommand
     * it is risky when changes in the Command file are not transferred here
     * To update this file just run: php artisan composer write-file
     */
    public function testCommandFileIsOlderThanThisTestFile(): void
    {
        $this->assertFirstClassIsOlderThanSecondClass(
            DemoCommand::class,
            __CLASS__
        );
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\Capital
     */
    public function testModelCapitalHas_2TestedRelationshipsAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\Capital");
        $this->assertModelBelongsTo('SchenkeIo\LaravelRelationManager\Demo\Models\Capital', 'SchenkeIo\LaravelRelationManager\Demo\Models\Country');
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\Country
     */
    public function testModelCountryHas_2TestedRelationshipsAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\Country");
        $this->assertModelHasOne('SchenkeIo\LaravelRelationManager\Demo\Models\Country', 'SchenkeIo\LaravelRelationManager\Demo\Models\Capital');
        $this->assertModelHasMany('SchenkeIo\LaravelRelationManager\Demo\Models\Country', 'SchenkeIo\LaravelRelationManager\Demo\Models\Region');
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\Region
     */
    public function testModelRegionHas_3TestedRelationshipsAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\Region");
        $this->assertModelBelongsTo('SchenkeIo\LaravelRelationManager\Demo\Models\Region', 'SchenkeIo\LaravelRelationManager\Demo\Models\Country');
        $this->assertModelHasMany('SchenkeIo\LaravelRelationManager\Demo\Models\Region', 'SchenkeIo\LaravelRelationManager\Demo\Models\City');
        $this->assertModelHasOneThrough('SchenkeIo\LaravelRelationManager\Demo\Models\Region', 'SchenkeIo\LaravelRelationManager\Demo\Models\Capital');
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\City
     */
    public function testModelCityHas_2TestedRelationshipsAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\City");
        $this->assertModelBelongsTo('SchenkeIo\LaravelRelationManager\Demo\Models\City', 'SchenkeIo\LaravelRelationManager\Demo\Models\Region');
        $this->assertModelIsManyToMany('SchenkeIo\LaravelRelationManager\Demo\Models\City', 'SchenkeIo\LaravelRelationManager\Demo\Models\HighWay');
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\HighWay
     */
    public function testModelHighWayHas_1TestedRelationshipAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\HighWay");
    }

    /**
     * Model SchenkeIo\LaravelRelationManager\Demo\Models\Single
     */
    public function testModelSingleHas_0TestedRelationshipsAndWorks(): void
    {
        $this->assertModelWorks("SchenkeIo\LaravelRelationManager\Demo\Models\Single");
    }
}
