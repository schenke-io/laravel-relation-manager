
## Testing Relationships

The package provides built-in tools to verify that your model implementation matches your `.relationships.json` file. This ensures that your documentation, diagrams, and actual code are always in sync.

### Strict vs. Loose Mode

- **Loose Mode (Default)**: Validates that every relationship defined in your `.relationships.json` file exists in your code. It ignores extra relationships in your code that are not defined in the JSON.
- **Strict Mode**: In addition to Loose Mode checks, it also fails if it finds relationships in your models that are *not* defined in your `.relationships.json` file. This is recommended for maintaining a complete and accurate documentation of your data model.

---

### PHPUnit Integration

To use with PHPUnit, create a test class that extends `AbstractRelationTest`.

```php
<?php

namespace Tests\Feature;

use SchenkeIo\LaravelRelationManager\Phpunit\AbstractRelationTest;

class RelationshipTest extends AbstractRelationTest
{
    /**
     * Optional: Path to your relationships file. 
     * Defaults to the one found by PathResolver (.relationships.json).
     */
    protected ?string $relationshipJsonPath = null;

    /**
     * Optional: Directory containing your models.
     * Defaults to the one defined in .relationships.json config 
     * or 'app/Models'.
     */
    protected ?string $modelDirectory = null;

    /**
     * Set to true for Strict Mode.
     */
    protected bool $strict = true;
}
```

The base class provides the following tests:
1. `test_laravel_environment`: Ensures the test runs within a Laravel environment.
2. `test_relationship_json_exists_and_is_valid`: Verifies that the JSON file is present and correctly formatted.
3. `test_models_match_json_state`: Compares the model implementation against the JSON definition.

---

### Pest PHP Integration

For Pest, you can use the `RelationTestBridge` to quickly register all necessary tests in a single call.

```php
<?php

use SchenkeIo\LaravelRelationManager\Pest\RelationTestBridge;

RelationTestBridge::all(
    relationshipJsonPath: null, // optional, defaults to PathResolver
    modelDirectory: null,       // optional, defaults to config or 'app/Models'
    strict: true                // recommended, defaults to false
);
```

This will automatically register three tests in your Pest file:
1. `test('laravel environment', ...)`
2. `test('relationship json exists and is valid', ...)`
3. `test('models match json state', ...)`
