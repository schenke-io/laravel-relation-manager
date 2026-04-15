---
name: relation-manager-testing
description: Test Eloquent relationships using laravel-relation-manager integrations
---

## When to use
- Verifying model relationships are correctly implemented
- Running relationship tests with Pest or PHPUnit
- Checking code matches the stored `.relationships.json` state

## Pest — `RelationTestBridge`

Register a full test suite in one call:

```php
use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;

RelationTestBridge::all(
    relationshipJsonPath: null, // defaults to config
    modelDirectory: null,       // defaults to config
    strict: false
);
```

Registers three tests: Laravel environment, JSON validity, models match JSON state.

### Fluent Pest Expectations

```php
expect(User::class)->toHasOne(Address::class);
expect(User::class)->toHasMany(Post::class);
expect(Post::class)->toBelongsTo(User::class);
expect(User::class)->toBelongsToMany(Role::class);
expect(User::class)->toHaveRelation(Post::class, EloquentRelation::hasMany);
```

## PHPUnit — `AbstractRelationTest`

Extend to get automatic relationship verification:

```php
use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;

class RelationshipTest extends AbstractRelationTest
{
    protected ?string $relationshipJsonPath = null; // defaults to config
    protected ?string $modelDirectory = null;       // defaults to config
    protected bool $strict = false;
}
```

Provides: `test_laravel_environment`, `test_relationship_json_exists_and_is_valid`, `test_models_match_json_state`.

## PHPUnit — `RelationTestTrait`

Use in any `TestCase` for manual assertions:

```php
use SchenkeIo\LaravelRelationManager\Phpunit\RelationTestTrait;

class ModelRelationTest extends TestCase
{
    use RelationTestTrait;

    public function test_user_relations(): void
    {
        $this->assertModelHasMany(User::class, Post::class);
        $this->assertModelBelongsTo(Post::class, User::class);
        $this->assertModelHasOne(User::class, Address::class);
        $this->assertModelBelongsToMany(User::class, Role::class);
    }
}
```

Available assertions: `assertModelHasOne`, `assertModelHasMany`, `assertModelBelongsTo`,
`assertModelBelongsToMany`, `assertModelHasRelation(model, related, EloquentRelation)`,
`assertLaravelEnvironment`, `assertRelationshipJsonExistsAndIsValid`, `assertModelsMatchJsonState`.