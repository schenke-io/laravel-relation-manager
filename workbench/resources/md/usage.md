
## Usage

This package's core functionality is provided by two components:
1) **Configuration File**: The `config/relation-manager.php` file allows you to define directories, files and namespaces of your project.
2) **Custom Relation Manager Command**: This command, which extends the `RelationManagerCommand` class, facilitates the configuration process.

In the command file:
- define models and their relations
- decide if you want to add reverse relations
- add BackedEnum classes used in $casts array of models as a special relation
- after the model-relation definition:
    - write the test file
    - run the test file (only)
    - write text and graphical documentation
    - echo tables

```php
// app/Console/Commands/RelationWriteCommand

use SchenkeIo\LaravelRelationManager\Console\RelationManagerCommand;

class RelationWriteCommand extends RelationManagerCommand 
{
    
    public function handle(): void
    {       
        
        $this->relationManager->model('Country')
            ->hasOne('Capital', true)
            ->hasMany('Region', true);
            
        $this->relationManager->model('Region')
            ->hasMany('City', true);
            
                        
        // repeat this for any model    

        // finally 
        $this->relationManager
            ->writeMarkdown()
            ->showTables()
            ->writeTest(strict: true)
            ->runTest();
                   
        
    }    
}

```

This command is called by default with `php artisan relation-manager:run`.

The following methods can be used inside `handle()`:

| method             | parameter                                          | details                                                      |
|--------------------|----------------------------------------------------|--------------------------------------------------------------|
| model($model)      | name of the model                                  | the model name is relative to the model namespace configured |
| writeTest($strict) | false: define the minimum, true: define everything | write the test file as defined                               |
| runTest            | -                                                  | run the test file                                            |
| writeMarkdown      | -                                                  | write a markdown file with the documentation                 |
| showTables         | -                                                  | Show the information as a table in the console               |
|                    |                                                    |                                                              |
