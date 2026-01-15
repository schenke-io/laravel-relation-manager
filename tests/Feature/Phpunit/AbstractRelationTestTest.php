<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;
use SchenkeIo\LaravelRelationManager\Tests\TestCase;

class ConcreteTest extends AbstractRelationTest
{
    protected ?string $modelDirectory = __DIR__.'/../../Models';
}

class AbstractRelationTestTest extends TestCase
{
    protected string $jsonPath = '.relationships.json';

    protected function setUp(): void
    {
        parent::setUp();
        File::put(base_path($this->jsonPath), json_encode([
            'models' => [],
        ]));
    }

    protected function tearDown(): void
    {
        if (File::exists(base_path($this->jsonPath))) {
            File::delete(base_path($this->jsonPath));
        }
        parent::tearDown();
    }

    public function test_abstract_relation_test_fails_in_strict_mode_with_empty_json()
    {
        $test = new class('test') extends AbstractRelationTest
        {
            protected ?string $modelDirectory = __DIR__.'/../../Models';

            protected bool $strict = true;
        };

        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        $test->test_models_match_json_state();
    }

    public function test_abstract_relation_test_passes_with_correct_json()
    {
        $json = [
            'models' => [
                \SchenkeIo\LaravelRelationManager\Tests\Models\User::class => [
                    'posts' => [
                        'type' => 'hasMany',
                        'related' => \SchenkeIo\LaravelRelationManager\Tests\Models\Post::class,
                        'foreignKey' => 'user_id',
                    ],
                    'roles' => [
                        'type' => 'belongsToMany',
                        'related' => \SchenkeIo\LaravelRelationManager\Tests\Models\Role::class,
                        'pivotTable' => 'role_user',
                    ],
                ],
                \SchenkeIo\LaravelRelationManager\Tests\Models\Post::class => [
                    'author' => [
                        'type' => 'belongsTo',
                        'related' => \SchenkeIo\LaravelRelationManager\Tests\Models\User::class,
                        'foreignKey' => 'author_id',
                    ],
                ],
                \SchenkeIo\LaravelRelationManager\Tests\Models\Role::class => [
                    'users' => [
                        'type' => 'belongsToMany',
                        'related' => \SchenkeIo\LaravelRelationManager\Tests\Models\User::class,
                        'pivotTable' => 'role_user',
                    ],
                ],
            ],
        ];
        File::put(base_path($this->jsonPath), json_encode($json));

        $test = new ConcreteTest('test');
        $test->test_laravel_environment();
        $test->test_relationship_json_exists_and_is_valid();
        $test->test_models_match_json_state();
    }
}
