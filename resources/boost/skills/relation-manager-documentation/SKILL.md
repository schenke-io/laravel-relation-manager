---
name: relation-manager-documentation
description: Manage and visualize Eloquent relationships using laravel-relation-manager
---

## When to use
- Scanning models for relationships and saving state to `.relationships.json`
- Generating Mermaid diagrams of model relationships
- Verifying code implementation matches the stored relationship state

## Artisan Commands

- `relation:extract` — Scan models, write `.relationships.json`
- `relation:verify` — Compare `.relationships.json` with current model state
- `relation:draw {filename?}` — Generate Markdown + Mermaid diagram (default: config `markdownPath`)

## `#[Relation]` Attribute

Declare relationships directly on model methods. Repeatable, targets classes or methods.

```php
use SchenkeIo\LaravelRelationManager\Attributes\Relation;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

#[Relation(EloquentRelation::hasMany, Comment::class, addReverse: true)]
public function comments(): HasMany
{
    return $this->hasMany(Comment::class);
}
```

Signature: `Relation(EloquentRelation $type, ?string $related = null, bool $addReverse = false)`

`addReverse: true` automatically injects the inverse relation on the related model.

## Supported Relation Types (`EloquentRelation` enum)

`hasOne`, `hasMany`, `hasOneThrough`, `hasManyThrough`, `belongsTo`, `belongsToMany`,
`morphTo`, `morphOne`, `morphMany`, `morphToMany`, `morphedByMany`

## `ModelScanner` Facade

```php
use SchenkeIo\LaravelRelationManager\Facades\ModelScanner;

$relations = ModelScanner::scan(?string $directory = null);
$columns   = ModelScanner::getDatabaseColumns(array $models);
```