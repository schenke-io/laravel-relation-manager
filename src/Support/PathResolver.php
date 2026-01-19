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

    public static ?string $mockBasePath = null;

    public static function getRealBasePath(string $path = ''): string
    {
        $base = self::$mockBasePath ?? base_path();
        $orchestraPath = 'vendor'.DIRECTORY_SEPARATOR.'orchestra'.DIRECTORY_SEPARATOR.'testbench-core'.DIRECTORY_SEPARATOR.'laravel';
        if (str_contains($base, $orchestraPath)) {
            $base = dirname($base, 4);
        }

        return $path ? $base.DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR) : $base;
    }

    public static function makePathRelative(string $path): string
    {
        $base = self::getRealBasePath();
        if (str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return ltrim($path, DIRECTORY_SEPARATOR);
    }

    public static function isPackage(): bool
    {
        return File::exists(self::getRealBasePath('src')) && File::exists(self::getRealBasePath('workbench'));
    }

    public static function isApp(): bool
    {
        return File::exists(self::getRealBasePath('app')) && File::exists(self::getRealBasePath('public'));
    }

    public static function getRelationshipFilePath(): string
    {
        $base = self::getRealBasePath();

        // 1. Check project root
        $path = $base.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
        if (File::exists($path)) {
            return $path;
        }

        // 2. Read composer.json -> extra.laravel-relation-manager.path
        $composerPath = $base.DIRECTORY_SEPARATOR.'composer.json';
        if (File::exists($composerPath)) {
            $composer = json_decode(File::get($composerPath), true);
            $relativePath = $composer['extra']['laravel-relation-manager']['path'] ?? null;
            if ($relativePath) {
                if (! str_contains($relativePath, 'vendor'.DIRECTORY_SEPARATOR)) {
                    $path = $base.DIRECTORY_SEPARATOR.$relativePath;
                    if (File::isDirectory($path)) {
                        return $path.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
                    }

                    return $path;
                }
            }
        }

        // 3. If Package: check /workbench/.relationships.json
        if (self::isPackage()) {
            $path = $base.DIRECTORY_SEPARATOR.'workbench'.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
            if (File::exists($path)) {
                return $path;
            }
        }

        // Fallback to basepath (ensuring it's not vendor)
        return $base.DIRECTORY_SEPARATOR.self::DEFAULT_FILENAME;
    }

    public static function getModelPath(): string
    {
        $base = self::getRealBasePath();

        // 1. Check .relationships.json
        $relPath = self::getRelationshipFilePath();
        if (File::exists($relPath)) {
            $data = json_decode(File::get($relPath), true);
            $modelPath = $data['config']['modelPath'] ?? null;
            if ($modelPath) {
                if (File::isDirectory($modelPath)) {
                    return $modelPath;
                }
                if (File::isDirectory($base.DIRECTORY_SEPARATOR.$modelPath)) {
                    return $base.DIRECTORY_SEPARATOR.$modelPath;
                }
            }
        }

        // 2. Check composer.json for extra.laravel-relation-manager.models
        $composerPath = $base.DIRECTORY_SEPARATOR.'composer.json';
        if (File::exists($composerPath)) {
            $composer = json_decode(File::get($composerPath), true);
            $relativeModelsPath = $composer['extra']['laravel-relation-manager']['models'] ?? null;
            if ($relativeModelsPath) {
                return $base.DIRECTORY_SEPARATOR.$relativeModelsPath;
            }
        }

        // 3. Try auth model
        $authModel = config('auth.providers.users.model');
        if ($authModel && class_exists($authModel)) {
            $reflection = new \ReflectionClass($authModel);
            $path = $reflection->getFileName();
            if ($path) {
                return dirname($path);
            }
        }

        // 4. App: Default to /app/Models
        if (self::isApp()) {
            return $base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models';
        }

        // 5. Package: Default to /src/Models
        if (self::isPackage()) {
            return $base.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Models';
        }

        // Final fallback
        if (File::exists($base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models')) {
            return $base.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Models';
        }

        return $base.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Models';
    }
}
