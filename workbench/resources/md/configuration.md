## Configuration

The package can be configured through the `.relationships.json` file or via `composer.json`.

### Path Discovery

The package uses a centralized path discovery for its primary data file, `.relationships.json`. The following order is used to resolve the path:

1.  **Project Root**: Checks for `.relationships.json` in the root of your project.
2.  **composer.json**: Checked for `extra.laravel-relation-manager.path`.
3.  **Workbench**: Checked for `workbench/.relationships.json`.

#### Customizing Path in `composer.json`

You can customize the path to your relationships file in your `composer.json`. This is particularly useful for package development:

```json
{
    "extra": {
        "laravel-relation-manager": {
            "path": "custom/path/to/relationships.json"
        }
    }
}
```

### General Settings

The following properties can be defined in the `config` section of your `.relationships.json` file:

| Property | Description | Default |
| --- | --- | --- |
| `markdown_path` | The destination path for generated markdown documentation via `relation:draw`. | `RELATIONS.md` |
| `model_path` | The directory (relative to project root) where your Eloquent models are scanned from. | `app/Models` |

Example `.relationships.json` with configuration:

```json
{
    "config": {
        "markdown_path": "docs/models.md",
        "model_path": "src/Models"
    },
    "models": {}
}
```

## Diagrams & Graphs

The package generates diagrams using [Mermaid.js](https://mermaid.js.org/).

### Graph Customization

- **Markdown Path**: The `markdown_path` setting in `config` determines where the diagram and relationship details are written when running `php artisan relation:draw`.
- **Pivot Table Toggle**: By default, the diagram hides explicit pivot tables for a cleaner overview. This behavior is controlled internally during diagram generation (referencing `withExtraPivotTables` in `RelationshipData`).
- **Diagram Tool**: Mermaid.js is the primary tool used. The generated diagrams are compatible with GitHub, GitLab, and most modern Markdown editors.

