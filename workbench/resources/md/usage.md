
## Workflow

Laravel Relation Manager helps you maintain consistency in your Eloquent relationships through a simple three-step process:

1. **Extract**: `php artisan relation:extract` - Scans your models and saves the relationship state to `.relationships.json`.
2. **Verify**: `php artisan relation:verify` - Ensures your code implementation matches the defined relationship state.
3. **Draw**: `php artisan relation:draw [filename]` - Generates visualization (diagrams and tables) of your model relationships.

### The Draw Command

The `relation:draw` command generates a comprehensive Markdown file (default: `RELATIONS.md`) that includes:
- **Model relations table**: Listing direct and indirect relations for each model.
- **Table relations diagram**: A visual representation of your database schema.
- **Database overview**: Expected tables and their foreign key columns.
- **Relationship details**: A complete list of all defined relationships.

You can optionally provide a filename to override the default path.

#### Understanding the Diagram

In the Mermaid diagram, arrows represent relationships between tables.
- **Colors**:
    - **Green** (`#2ecc71`): Standard Eloquent relations (One-to-One, One-to-Many).
    - **Blue** (`#3498db`): Polymorphic relations.
    - **Orange** (`#e67e22`): Many-to-Many relations.
- **Line Styles**:
    - `==>` : Standard direct relations.
    - `-->` : Polymorphic relations.
    - `<==>` : Many-to-Many relations.

**FAQ: Why is there an arrow from `tags` to `regions`?**
This occurs when a model (like `Tag`) has a relationship method pointing to another model (like `Region`). Even if the database foreign key is on a pivot table or the other model, the diagram reflects the intent of the relationship method defined in the model.

**Suggestion**: If you want to exclude certain methods from the diagram, use the `#[Relation(EloquentRelation::noRelation)]` attribute.

### Configuration

The `.relationships.json` file contains a `config` section to customize the behavior:

- `markdown_path`: Path where the relations documentation will be generated (default: `RELATIONS.md`).
- `model_path`: Directory where your Eloquent models are located (default: `app/Models`).
- `use_mermaid`: Boolean to toggle between Mermaid (default) and Graphviz diagram generation.
