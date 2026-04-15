<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Feature\Phpunit;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\ExpectationFailedException;
use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;
use SchenkeIo\LaravelRelationManager\Support\PathResolver;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;
use SchenkeIo\LaravelRelationManager\Tests\Models\Role;
use SchenkeIo\LaravelRelationManager\Tests\Models\User;
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
        File::put(PathResolver::getRealBasePath($this->jsonPath), json_encode([
            'models' => [],
        ]));
    }

    protected function tearDown(): void
    {
        if (File::exists(PathResolver::getRealBasePath($this->jsonPath))) {
            File::delete(PathResolver::getRealBasePath($this->jsonPath));
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

        $this->expectException(ExpectationFailedException::class);
        $test->test_models_match_json_state();
    }

    public function test_abstract_relation_test_passes_with_correct_json()
    {
        $json = [
            'models' => [
                User::class => [
                    'posts' => [
                        'type' => 'hasMany',
                        'related' => Post::class,
                        'foreignKey' => 'user_id',
                    ],
                    'roles' => [
                        'type' => 'belongsToMany',
                        'related' => Role::class,
                        'pivotTable' => 'role_user',
                    ],
                ],
                Post::class => [
                    'author' => [
                        'type' => 'belongsTo',
                        'related' => User::class,
                        'foreignKey' => 'author_id',
                    ],
                ],
                Role::class => [
                    'users' => [
                        'type' => 'belongsToMany',
                        'related' => User::class,
                        'pivotTable' => 'role_user',
                    ],
                ],
            ],
        ];
        File::put(PathResolver::getRealBasePath($this->jsonPath), json_encode($json));

        $test = new ConcreteTest('test');
        $test->test_laravel_environment();
        $test->test_relationship_json_exists_and_is_valid();
        $test->test_models_match_json_state();
    }
}
