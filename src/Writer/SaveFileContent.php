<?php

namespace SchenkeIo\LaravelRelationManager\Writer;

use SchenkeIo\LaravelRelationManager\Exceptions\DirectoryNotWritableException;

class SaveFileContent
{
    public function saveContent(string $filename, string $data): void
    {
        $dirName = dirname($filename);
        if (strlen($dirName) > 1 && is_writable($dirName)) {
            file_put_contents($filename, $data);
        } else {
            throw new DirectoryNotWritableException("directory $dirName is not writable");
        }
    }
}
