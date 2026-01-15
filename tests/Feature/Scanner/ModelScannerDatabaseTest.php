<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SchenkeIo\LaravelRelationManager\Scanner\ModelScanner;
use SchenkeIo\LaravelRelationManager\Tests\Models\Post;

test('it can find potential relations from database', function () {
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('author_id');
        $table->unsignedBigInteger('unrelated_id');
        $table->timestamps();
    });

    $scanner = new ModelScanner;
    $results = $scanner->scan(__DIR__.'/../../Models');
    $databaseColumns = $scanner->getDatabaseColumns($results);

    expect($databaseColumns)->toHaveKey(Post::class)
        ->and($databaseColumns[Post::class])->not->toContain('author_id')
        ->and($databaseColumns[Post::class])->toContain('unrelated_id')
        ->and($databaseColumns[Post::class])->not->toContain('id');
});
