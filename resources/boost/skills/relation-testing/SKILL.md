---
name: relation-testing
description: Test Eloquent relationships using laravel-relation-manager integrations
---

## When to use
- When you want to ensure model relationships are correctly implemented.
- When you want to use Pest or PHPUnit for relationship testing.
- When you need to verify if the code matches a predefined relationship state.

## Features

### Pest Integration
Quickly register a full suite of relationship tests using `RelationTestBridge::all()`.

```php
use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;

RelationTestBridge::all(
    strict: true
);
```

You can also use fluent expectations:

```php
it('has correct relations', function () {
    expect(User::class)->toHasMany(Post::class);
});
```

### PHPUnit Integration
Extend `AbstractRelationTest` to get automatic relationship verification.

```php
namespace Tests\Feature;

use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;

class RelationshipTest extends AbstractRelationTest
{
    protected bool $strict = true;
}
```

### Manual Assertions
Use `RelationTestTrait` in your PHPUnit tests for manual assertions.

```php
use SchenkeIo\LaravelRelationManager\Phpunit\RelationTestTrait;

class ModelRelationTest extends TestCase
{
    use RelationTestTrait;

    public function test_user_has_many_posts()
    {
        $this->assertModelHasMany(User::class, Post::class);
    }
}
```
