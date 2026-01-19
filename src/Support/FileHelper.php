<?php

namespace SchenkeIo\LaravelRelationManager\Support;

use Illuminate\Support\Facades\File;

/**
 * Helper class for file and class-related operations.
 */
class FileHelper
{
    /**
     * Extracts the full qualified class name from a PHP file by parsing its namespace and class name.
     *
     * @param  string  $path  The full path to the PHP file.
     * @return string|null The fully qualified class name or null if not found.
     */
    public static function getClassNameFromFile(string $path): ?string
    {
        $content = File::get($path);
        if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
            $namespace = $matches[1];
            if (preg_match('/class\s+(\w+)/', $content, $matches)) {
                return $namespace.'\\'.$matches[1];
            }
        }

        return null;
    }
}
