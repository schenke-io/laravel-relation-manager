---
name: relation-management
description: Manage and visualize Eloquent relationships using laravel-relation-manager
---

## When to use
- When you need to scan models for relationships.
- When you want to visualize model relationships in Markdown.
- When you want to ensure the code implementation matches the intended relationship state.

## Features

### Relationship Extraction
Use `php artisan relation:extract` to scan your models and save the relationship state to `.relationships.json`.

### Relationship Visualization
Use `php artisan relation:draw` to generate a comprehensive Markdown file (default: `RELATIONS.md`) with:
- Model relations table.
- Table relations diagram (Mermaid or Graphviz).
- Database overview.

### Relationship Verification
Use `php artisan relation:verify` to ensure that your code matches the state defined in `.relationships.json`.

### Declarative Relationships
Use the `#[Relation]` attribute to define relationships directly on model methods, which can also automatically inject reverse relations.

```php
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

#[Relation(EloquentRelation::hasMany, Comment::class, addReverse: true)]
public function comments(): HasMany
{
    return $this->hasMany(Comment::class);
}
```
