## Configuration

The package can be configured through the `.relationships.json` file or via `composer.json`.

### Path Discovery

The package uses a centralized path discovery for its primary data file, `.relationships.json`. The following order is used to resolve the path:

1.  **Project Root**: Checks for `.relationships.json` in the root of your project.
2.  **composer.json**: Checked for `extra.laravel-relation-manager.path`.
3.  **Workbench**: Checked for `workbench/.relationships.json` (if developed as a package).

#### Customizing Paths in `composer.json`

You can customize the paths in your `composer.json`. This is particularly useful for package development:

```json
{
    "extra": {
        "laravel-relation-manager": {
            "path": "custom/path/to/relationships.json",
            "models": "src/Domain/Models"
        }
    }
}
```

### Model Discovery

The directory to scan for Eloquent models is resolved in the following order:

1.  **.relationships.json**: The `model_path` setting in the `config` section.
2.  **composer.json**: The `extra.laravel-relation-manager.models` setting.
3.  **Auth Configuration**: The model class defined in `config('auth.providers.users.model')`.
4.  **Defaults**:
    - **App Mode**: `app/Models` (detected by presence of `/app` and `/public`).
    - **Package Mode**: `src/Models` (detected by presence of `/src` and `/workbench`).

### General Settings

The following properties can be defined in the `config` section of your `.relationships.json` file:

| Property | Description | Default |
| --- | --- | --- |
| `markdown_path` | The destination path for generated markdown documentation via `relation:draw`. | `RELATIONS.md` |
| `model_path` | The directory (relative to project root) where your Eloquent models are scanned from. | `app/Models` |
| `use_mermaid` | Whether to use Mermaid for diagram generation. | `true` |

Example `.relationships.json` with configuration:

```json
{
    "config": {
        "markdown_path": "docs/models.md",
        "model_path": "src/Models",
        "use_mermaid": true
    },
    "models": {}
}
```

## Diagrams & Graphs

The package generates diagrams using [Mermaid.js](https://mermaid.js.org/).

### Graph Customization

- **Markdown Path**: The `markdown_path` setting in `config` determines where the diagram and relationship details are written when running `php artisan relation:draw`.
- **Pivot Table Toggle**: By default, the diagram hides explicit pivot tables for a cleaner overview. This behavior is controlled internally during diagram generation (referencing `withExtraPivotTables` in `RelationshipData`).
- **Diagram Tool**: Mermaid.js is the primary tool used (`use_mermaid: true`). If disabled, the package attempts to use **Graphviz** (via the `dot` command) to generate a PNG diagram.
- **Compatibility**: Mermaid diagrams are compatible with GitHub, GitLab, and most modern Markdown editors. Graphviz requires the `dot` utility to be installed on your system.

