<?php

namespace SchenkeIo\LaravelRelationshipManager\Define;

use SchenkeIo\LaravelRelationshipManager\Data\ClassData;
use SchenkeIo\LaravelRelationshipManager\Data\ProjectData;
use SchenkeIo\LaravelRelationshipManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationshipManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateMermaidMarkdown;
use SchenkeIo\LaravelRelationshipManager\Writer\GenerateProjectTestFile;
use SchenkeIo\LaravelRelationshipManager\Writer\SaveFileContent;

class Project
{
    /**
     * @param  SaveFileContent  $saveFileContent extra parameter to simplify testing
     */
    public function __construct(
        protected ProjectData $projectData,
        protected SaveFileContent $saveFileContent
    ) {
        /*
         * list all warnings from projectData
         */
        foreach ($this->projectData->getErrors() as $error) {
            $this->projectData->command->warn($error);
        }
    }

    /**
     * scan directories for missing models
     *
     * @param  string[]  $directories
     * @return $this
     */
    public function addModelDirectories(array $directories): self
    {
        $this->projectData->scanDirectoriesForModels($directories);

        return $this;
    }

    /**
     * @throws DirectoryNotWritableException
     */
    public function writeMermaidMarkdown(string $fileName, bool $isLeftToRight = true): self
    {
        $this->saveFileContent->saveContent(
            $fileName,
            GenerateMermaidMarkDown::getContent(
                $this->projectData,
                $isLeftToRight
            )
        );

        return $this;
    }

    /**
     * @throws InvalidClassException|DirectoryNotWritableException
     */
    public function writeTestFileClassPhpunit(string $testFilePhpunit, bool $andRun = false): self
    {
        $classData = ClassData::take($testFilePhpunit);
        if ($classData->isClass) {
            $this->saveFileContent->saveContent(
                $classData->fileName,
                GenerateProjectTestFile::getContent(
                    $this->projectData,
                    $classData
                )
            );
            $this->projectData->command->info("class written: $testFilePhpunit");
            if ($andRun) {
                $this->projectData->command->call('test', [
                    'filter' => $testFilePhpunit,
                ]
                );
            }
        } else {
            $this->projectData->command->error("invalid file/class: $testFilePhpunit");
        }

        return $this;
    }
}
