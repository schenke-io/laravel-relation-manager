<?php

namespace SchenkeIo\LaravelRelationManager\Define;

use SchenkeIo\LaravelRelationManager\Data\ClassData;
use SchenkeIo\LaravelRelationManager\Data\ProjectData;
use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;
use SchenkeIo\LaravelRelationManager\Tests\Define\TestProjectTest;
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
     * @throws DirectoryNotWritableException
     */
    public function writeTestFileClassPhpunit(
        string $testFilePhpunit,
        bool $andRun = false,
        string $baseTestFile = 'Tests/TestCase'
    ): self {
        if ($testFilePhpunit == TestProjectTest::class) {
            $fileName = __DIR__.'/../../tests/Define/TestProjectTest.php';
            $className = 'TestProjectTest';
            $nameSpace = 'SchenkeIo\LaravelRelationManager\Tests\Define';
            $baseTestFile = 'SchenkeIo\LaravelRelationManager\Tests\TestCase';
            $isClass = true;
        } else {
            $classData = ClassData::take($testFilePhpunit);
            $isClass = $classData->isClass;
            $fileName = $classData->fileName;
            $className = $classData->className;
            $nameSpace = $classData->nameSpace;
        }

        if ($isClass) {
            $this->saveFileContent->saveContent(
                $fileName,
                GenerateProjectTestFile::getContent(
                    $this->projectData,
                    $className,
                    $nameSpace,
                    $baseTestFile
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
