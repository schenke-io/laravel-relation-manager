
## Workflow

Laravel Relation Manager helps you maintain consistency in your Eloquent relationships through a simple three-step process:

1. **Extract**: `php artisan relation:extract` - Scans your models and saves the relationship state to `.relationships.json`.
2. **Verify**: `php artisan relation:verify` - Ensures your code implementation matches the defined relationship state.
3. **Draw**: `php artisan relation:draw` - Generates visualization (diagrams and tables) of your model relationships.
