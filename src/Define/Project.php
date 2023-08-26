<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationManager\Exceptions\InvalidClassException;
use SchenkeIo\LaravelRelationManager\Writer\GenerateMermaidMarkdown;
use SchenkeIo\LaravelRelationManager\Writer\GenerateProjectTestFile;
use SchenkeIo\LaravelRelationManager\Writer\SaveFileContent;

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
