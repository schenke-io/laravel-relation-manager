## Overview
Laravel Relation Manager allows you to plan, document, and test Eloquent model relationships. It provides tools to extract relationship definitions from your code, verify their implementation, and generate visual diagrams.

## Key Concepts
- **Extraction**: Scans models for relationships and saves them to `.relationships.json`.
- **Verification**: Checks if the current code implementation matches the saved state.
- **Visualization**: Generates Mermaid or Graphviz diagrams of your model relations.

## Artisan Commands
- `php artisan relation:extract`: Scans models and updates `.relationships.json`.
- `php artisan relation:verify`: Compares code with the defined relationship state.
- `php artisan relation:draw [filename]`: Generates a Markdown file (default: `RELATIONS.md`) with diagrams and tables.

## Attributes
Use the `#[Relation]` attribute to provide additional metadata or suppress relationships.

@verbatim
<code-snippet name="Declarative Relation with Reverse" lang="php">
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

class User extends Model
{
    #[Relation(EloquentRelation::hasMany, Comment::class, addReverse: true)]
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
</code-snippet>

<code-snippet name="Suppress Relationship" lang="php">
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

class User extends Model
{
    #[Relation(EloquentRelation::noRelation)]
    public function internalMethod()
    {
        // ignored by scanner
    }
}
</code-snippet>
@endverbatim

## Testing
The package integrates with Pest and PHPUnit to ensure relationships stay consistent.

### Pest Integration
@verbatim
<code-snippet name="Pest Full Suite" lang="php">
use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;

RelationTestBridge::all(
    strict: true
);
</code-snippet>

<code-snippet name="Pest Expectations" lang="php">
it('has correct relations', function () {
    expect(User::class)->toHasMany(Post::class);
    expect(Post::class)->toBelongsTo(User::class);
});
</code-snippet>
@endverbatim

### PHPUnit Integration
@verbatim
<code-snippet name="PHPUnit Test Class" lang="php">
namespace Tests\Feature;

use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;

class RelationshipTest extends AbstractRelationTest
{
    protected bool $strict = true;
}
</code-snippet>
@endverbatim
