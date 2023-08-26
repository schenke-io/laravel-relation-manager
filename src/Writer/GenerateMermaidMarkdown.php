<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Data\RelationData;

class GenerateMermaidMarkdown
{
    public static function getContent(ProjectData $projectData, bool $isLeftToRight = true): string
    {
        $commandClass = $projectData->commandClassName;
        $signature = $projectData->signature;

        $direction = $isLeftToRight ? 'LR' : 'TD';

        $content = <<<markdown
<!--
written with: php artisan $signature
by Console Command $commandClass
do not manually edit this file as it will be overwritten

-->
```mermaid
flowchart $direction

markdown;
        foreach ($projectData->getAllModels() as $baseModel => $relatedModels) {
            foreach ($relatedModels as $otherModel => $relation) {
                $relationData = new RelationData($baseModel, $otherModel, $relation->name);
                $content .= $relationData->getMermaidLine();
            }
        }
        $content .= "```\n";

        return $content;
    }
}
