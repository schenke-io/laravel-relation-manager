<?php

namespace SchenkeIo\LaravelRelationManager\Support;

use Illuminate\Support\Facades\File;

/**
 * Utility to resolve the path of the .relationships.json file
 * by checking multiple locations (root, composer.json extra, or workbench).
 */
class PathResolver
{
    public const DEFAULT_FILENAME = '.relationships.json';

    public static function getRelationshipFilePath(): string
    {
        $searchPaths = array_unique([base_path(), getcwd()]);

        foreach ($searchPaths as $searchPath) {
            // 1. Check for .relationships.json
            $path = $searchPath.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
            if (File::exists($path)) {
                return $path;
            }

            // 2. Read composer.json -> extra.laravel-relation-manager.path
            $composerPath = $searchPath.DIRECTORY_SEPARATOR.'composer.json';
            if (File::exists($composerPath)) {
                $composer = json_decode(File::get($composerPath), true);
                $path = $composer['extra']['laravel-relation-manager']['path'] ?? null;
                if ($path) {
                    return $searchPath.DIRECTORY_SEPARATOR.$path;
                }
            }

            // 3. Check for workbench/.relationships.json
            $workbenchPath = $searchPath.DIRECTORY_SEPARATOR.'workbench'.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
            if (File::exists($workbenchPath)) {
                return $workbenchPath;
            }
        }

        // Fallback to basepath
        return base_path(self::DEFAULT_FILENAME);
    }
}
