<?php

/**
 * ## Test of all models defined
 *
 * ------
 *
 * This file is auto-generated by: Workbench\App\Console\Commands\RunTestProjectManagerCommand
 * rewrite this test-file on the console with: php artisan run:test-project
 */

namespace SchenkeIo\LaravelRelationManager\Tests\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use SchenkeIo\LaravelRelationManager\Phpunit\AssertModelRelations;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;
use Workbench\App\Console\Commands\RunTestProjectManagerCommand;

class TestProjectTest extends TestCase
{
    use RefreshDatabase;
    use AssertModelRelations;

    /**
     * @return void
     *
     * Since this class is written by the Command file Workbench\App\Console\Commands\RunTestProjectManagerCommand
     * it is risky when changes in the Command file are not transferred here
     * To update this file just run: php artisan run:test-project
     */
    #[Group('GenerateProjectTestFile')]
    public function testCommandFileIsOlderThanThisTestFile(): void
    {
        $this->assertFirstClassIsOlderThanSecondClass(
            RunTestProjectManagerCommand::class,
            __CLASS__
        );
    }

    /**
     * Model Workbench\App\Models\Capital
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelCapitalHas_2StrictRelationshipsAndWorks(): void
    {
        $this->assertModelBelongsTo('Workbench\App\Models\Capital', 'Workbench\App\Models\Country');
        $this->assertModelMorphOne('Workbench\App\Models\Capital', 'Workbench\App\Models\Location');
        $this->assertModelRelationCount('Workbench\App\Models\Capital', 2);
    }

    /**
     * Model Workbench\App\Models\City
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelCityHas_4StrictRelationshipsAndWorks(): void
    {
        $this->assertModelIsManyToMany('Workbench\App\Models\City', 'Workbench\App\Models\Highway');
        $this->assertModelMorphOne('Workbench\App\Models\City', 'Workbench\App\Models\Location');
        $this->assertModelHasOneThrough('Workbench\App\Models\City', 'Workbench\App\Models\Country');
        $this->assertModelBelongsTo('Workbench\App\Models\City', 'Workbench\App\Models\Region');
        $this->assertModelRelationCount('Workbench\App\Models\City', 4);
    }

    /**
     * Model Workbench\App\Models\Country
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelCountryHas_3StrictRelationshipsAndWorks(): void
    {
        $this->assertModelHasOne('Workbench\App\Models\Country', 'Workbench\App\Models\Capital');
        $this->assertModelHasMany('Workbench\App\Models\Country', 'Workbench\App\Models\Region');
        $this->assertModelHasManyThrough('Workbench\App\Models\Country', 'Workbench\App\Models\City');
        $this->assertModelRelationCount('Workbench\App\Models\Country', 3);
    }

    /**
     * Model Workbench\App\Models\Highway
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelHighwayHas_2StrictRelationshipsAndWorks(): void
    {
        $this->assertModelBelongsToMany('Workbench\App\Models\Highway', 'Workbench\App\Models\City');
        $this->assertModelMorphMany('Workbench\App\Models\Highway', 'Workbench\App\Models\Location');
        $this->assertModelRelationCount('Workbench\App\Models\Highway', 2);
    }

    /**
     * Model Workbench\App\Models\Location
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelLocationHas_1StrictRelationshipAndWorks(): void
    {
        $this->assertModelMorphTo('Workbench\App\Models\Location');
        $this->assertModelRelationCount('Workbench\App\Models\Location', 1);
    }

    /**
     * Model Workbench\App\Models\Region
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelRegionHas_3StrictRelationshipsAndWorks(): void
    {
        $this->assertModelBelongsTo('Workbench\App\Models\Region', 'Workbench\App\Models\Country');
        $this->assertModelHasMany('Workbench\App\Models\Region', 'Workbench\App\Models\City');
        $this->assertModelHasOneThrough('Workbench\App\Models\Region', 'Workbench\App\Models\Capital');
        $this->assertModelRelationCount('Workbench\App\Models\Region', 3);
    }

    /**
     * Model Workbench\App\Models\Single
     */
    #[Group('GenerateProjectTestFile')]
    public function testModelSingleHas_0StrictRelationshipsAndWorks(): void
    {
        $this->assertModelRelationCount('Workbench\App\Models\Single', 0);
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table capitals exists')]
    public function testDatabaseTable_capitalsExists()
    {
        $this->assertTrue(Schema::hasTable('capitals'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table capitals has field country_id')]
    public function testDatabaseField_country_idExistsIn_capitals()
    {
        $this->assertTrue(Schema::hasColumn('capitals','country_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table cities exists')]
    public function testDatabaseTable_citiesExists()
    {
        $this->assertTrue(Schema::hasTable('cities'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table cities has field region_id')]
    public function testDatabaseField_region_idExistsIn_cities()
    {
        $this->assertTrue(Schema::hasColumn('cities','region_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table city_highway exists')]
    public function testDatabaseTable_city_highwayExists()
    {
        $this->assertTrue(Schema::hasTable('city_highway'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table city_highway has field city_id')]
    public function testDatabaseField_city_idExistsIn_city_highway()
    {
        $this->assertTrue(Schema::hasColumn('city_highway','city_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table city_highway has field highway_id')]
    public function testDatabaseField_highway_idExistsIn_city_highway()
    {
        $this->assertTrue(Schema::hasColumn('city_highway','highway_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table countries exists')]
    public function testDatabaseTable_countriesExists()
    {
        $this->assertTrue(Schema::hasTable('countries'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table highways exists')]
    public function testDatabaseTable_highwaysExists()
    {
        $this->assertTrue(Schema::hasTable('highways'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table locations exists')]
    public function testDatabaseTable_locationsExists()
    {
        $this->assertTrue(Schema::hasTable('locations'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table locations has field locationable_id')]
    public function testDatabaseField_locationable_idExistsIn_locations()
    {
        $this->assertTrue(Schema::hasColumn('locations','locationable_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table locations has field locationable_type')]
    public function testDatabaseField_locationable_typeExistsIn_locations()
    {
        $this->assertTrue(Schema::hasColumn('locations','locationable_type'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table regions exists')]
    public function testDatabaseTable_regionsExists()
    {
        $this->assertTrue(Schema::hasTable('regions'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table regions has field country_id')]
    public function testDatabaseField_country_idExistsIn_regions()
    {
        $this->assertTrue(Schema::hasColumn('regions','country_id'));
    }

    #[Group('GenerateProjectTestFile')]
    #[TestDox('table singles exists')]
    public function testDatabaseTable_singlesExists()
    {
        $this->assertTrue(Schema::hasTable('singles'));
    }
}
