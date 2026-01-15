<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use Illuminate\Support\Facades\File;
use SchenkeIo\LaravelRelationManager\Data\RelationshipData;
use SchenkeIo\LaravelRelationManager\Enums\DiagramDirection;
use SchenkeIo\LaravelRelationManager\Enums\EloquentRelation;

/**
 * Service to generate a markdown file containing a Mermaid diagram
 * and a table with relationship details.
 */
class GenerateMarkdownFile
{
    /** @var string[] */
    protected array $errors = [];

    public function __construct(protected RelationshipData $relationshipData) {}

    public function generate(string $filename, DiagramDirection $direction = DiagramDirection::LR): bool
    {
        $this->errors = [];
        $markdown = "# Relationships\n\n";

        $markdown .= "## 1. Model relations\n\n";
        $markdown .= $this->getModelRelationsTable()."\n\n";

        $markdown .= "## 2. Table relations (Diagram)\n\n";
        if ($this->relationshipData->config->useMermaid) {
            $markdown .= $this->getMermaidDiagram($direction);
        } else {
            $success = GetDiagram::writeGraphvizFile(
                $this->relationshipData->getDiagramData(),
                $direction,
                $filename
            );
            if ($success) {
                $markdown .= GetDiagram::getGraphvizCode();
            } else {
                $errorMessage = "Graphviz generation failed. Please ensure 'dot' is installed and in your PATH.";
                $markdown .= "> [!WARNING]\n";
                $markdown .= "> $errorMessage\n";
                $this->errors[] = $errorMessage;
            }
        }

        $markdown .= "\n\n## 3. Database\n\n";
        $markdown .= $this->getDatabaseTable();

        $markdown .= "\n\n## 4. Relationship Details\n\n";
        $markdown .= $this->getRelationshipTable();

        return (bool) File::put($filename, $markdown);
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getMermaidDiagram(DiagramDirection $direction = DiagramDirection::LR): string
    {
        $diagramData = $this->relationshipData->getDiagramData();

        return GetDiagram::getMermaidCode($diagramData, $direction);
    }

    public function getModelRelationsTable(): string
    {
        $headers = ['Model', 'Direct', 'Indirect'];
        $rows = [];
        foreach ($this->relationshipData->getModelRelationsData() as $model => $data) {
            $rows[] = [
                $model,
                implode('<br>', $data['direct']),
                implode('<br>', $data['indirect']),
            ];
        }

        return GetTable::getMarkdown([$headers, $rows]);
    }

    public function getDatabaseTable(): string
    {
        $headers = ['Table', 'Required Fields'];
        $rows = [];
        foreach ($this->relationshipData->getDatabaseTableData() as $table => $fields) {
            $rows[] = [
                $table,
                implode(', ', $fields),
            ];
        }

        return GetTable::getMarkdown([$headers, $rows]);
    }

    public function getRelationshipTable(): string
    {
        $headers = ['Model', 'Method(): Relation', 'Related Model', 'Reverse Relation'];
        $rows = [];
        foreach ($this->relationshipData->models as $model => $modelData) {
            foreach ($modelData->methods as $method => $data) {
                $type = $data->type;
                $typeName = $type->name;

                $relatedModel = 'n/a';
                if ($type === EloquentRelation::morphTo) {
                    $targets = $this->relationshipData->getMorphToTargets($model, $method);
                    $relatedModel = implode(', ', array_map(fn ($t) => class_basename($t), $targets));
                    if (empty($relatedModel)) {
                        $relatedModel = 'n/a';
                    }
                } elseif ($data->related && $data->related !== 'n/a') {
                    $relatedModel = class_basename($data->related);
                }

                $reverseRelations = $this->relationshipData->getReverseRelations($model, $method);
                $reverseStr = implode(', ', array_map(fn ($r) => class_basename($r[0]).'::'.$r[1], $reverseRelations));
                if (empty($reverseStr)) {
                    $reverseStr = 'n/a';
                }

                $rows[] = [
                    class_basename($model),
                    "<code>$method(): $typeName</code>",
                    $relatedModel,
                    $reverseStr,
                ];
            }
        }

        return GetTable::getHtml([$headers, $rows], [0]);
    }
}
