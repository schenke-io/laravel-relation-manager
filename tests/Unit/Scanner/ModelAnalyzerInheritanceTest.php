<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit\Scanner;

use SchenkeIo\LaravelRelationManager\Scanner\ModelAnalyzer;
use SchenkeIo\LaravelRelationManager\Tests\Models\ExtendedUser;
use SchenkeIo\LaravelRelationManager\Tests\Models\TraitModel;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ModelAnalyzerInheritanceTest extends TestCase
{
    public function test_it_finds_inherited_relationships()
    {
        $analyzer = new ModelAnalyzer(ExtendedUser::class);
        $models = [];
        $results = $analyzer->analyze($models);

        /*
         * Current implementation skips methods not declared in the class.
         * ExtendedUser inherits 'posts' and 'roles' from User.
         */
        $this->assertArrayHasKey('posts', $results, 'Inherited relationship "posts" not found');
        $this->assertArrayHasKey('roles', $results, 'Inherited relationship "roles" not found');
    }

    public function test_it_finds_trait_relationships()
    {
        $analyzer = new ModelAnalyzer(TraitModel::class);
        $models = [];
        $results = $analyzer->analyze($models);

        /*
         * Current implementation skips methods not declared in the class.
         * TraitModel uses HasRelationTrait which defines 'traitPosts'.
         */
        $this->assertArrayHasKey('traitPosts', $results, 'Trait relationship "traitPosts" not found');
    }
}
