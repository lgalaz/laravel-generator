<?php namespace App\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Composer;
use Illuminate\Filesystem\Filesystem;

class CRUDControllerMakeCommand extends Command
{
    protected $signature = 'make:crud
                        {name : the name of the model}
                        {--schema= : the schema for the migration}',
              $description = 'Create classes for crud operations on model',
              $files,
              $composer,
              $className;

    private $config = [
        'Validator' => [
            'path' => './app/Services/Validators/',
            'namespace' => 'App\Services\Validators',
            'errorMsg' => 'could not create validator, filename exsits.',
            'warrningMsg' => 'the abstract validator already exists.'
        ],
        'Repository' => [
            'path' => './app/Repositories/',
            'namespace' => 'App\Repositories',
            'errorMsg' => 'could not create repository, filename exsits.',
            'warrningMsg' => 'the abstract repository already exists.'
        ],
        'Controller' => [
            'path' => './app/Http/Controllers/',
            'namespace' => 'App\Http\Controllers',
            'errorMsg' => 'could not create controller, filename exsits.',
            'warrningMsg' => 'the abstract controller already exists.'
        ]
    ];


    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    public function handle()
    {
        $this->className = ucwords(str_singular(camel_case($this->argument('name'))));

        $this->call('make:migration:schema', [
            'name' => 'create_' . $this->argument('name') . '_table',
            '--schema' => $this->option('schema')
        ]);

        $this->makeCRUD();
    }

    protected function makeCRUD()
    {
        $this->createClasses('Validator');
        $this->createClasses('Repository');
        $this->createClasses('Controller');
        $this->addRoute();

        $this->composer->dumpAutoloads();
    }

    private function createClasses($type)
    {
        if ($this->files->exists($path = $this->getPath('Abstract',$type))) {
            $this->info($this->config[$type]['warrningMsg']);
        } else {
            $this->makeDirectory($path);

            $this->files->put($path, $this->compileAbstract($type));
        }

        if ($this->files->exists($path = $this->getPath($this->className, $type))) {
            return $this->error($this->config[$type]['errorMsg']);
        }

        $this->files->put($path, $this->compileStub($type));

        $this->info($type. ' created successfully.');
    }

    protected function getPath($name, $type)
    {
        return $this->config[$type]['path'] . $name.$type. '.php';
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    protected function compileStub($type)
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/' . strtolower($type) . '.stub');

        $this->replaceClassName($stub);

        $stub = str_replace('{{validator.namespace}}', $this->config['Validator']['namespace'], $stub);

        $stub = str_replace('{{repository.namespace}}', $this->config['Repository']['namespace'], $stub);

        $stub = str_replace('{{controller.namespace}}', $this->config['Controller']['namespace'], $stub);

        return $stub;
    }

    protected function compileAbstract($type)
    {
        $abstract = $this->files->get(__DIR__ . '/../stubs/Abstract' . $type . '.stub');

        $abstract = str_replace('{{namespace}}', $this->config[$type]['namespace'], $abstract);

        return $abstract;
    }

    protected function addRoute()
    {
        $stub = $this->files->get(__DIR__ . '/../stubs/route.stub');

        $this->className = strtolower($this->className);

        $this->replaceClassName($stub);

        $this->files->append('./app/Http/routes.php', $stub);
    }

    protected function replaceClassName(&$stub)
    {
        $stub = str_replace('{{class}}', $this->className, $stub);

        return $this;
    }

    protected function replaceNamespace(&$stub, $namespace)
    {
        $stub = str_replace('{{namespace}}', $namespace, $stub);

        return $this;
    }
}
