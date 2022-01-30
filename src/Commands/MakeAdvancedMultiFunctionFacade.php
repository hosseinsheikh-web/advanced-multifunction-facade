<?php

namespace Palpalasi\AdvancedMultifunctionFacade\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeAdvancedMultiFunctionFacade extends GeneratorCommand
{
    protected $name = 'palpalasi:make-facade {name}';
    protected $description = 'Create a new advanced multi function facade';

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);

        $this->files = $files;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        $className = $this->getSingularClassName($this->argument('name'));
        $className = Str::contains($className, 'Facade') ? $className : $className . "Facade";

        return $className;
    }

    protected function getStub()
    {
        // TODO: Implement getStub() method.
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        if ($this->option('authorize') && $this->option('calling')) {
            return __DIR__ . '/stubs/facade_full_function.stub';
        } elseif ($this->option('authorize')) {
            return __DIR__ . '/stubs/facade_with_only_auth.stub';
        } elseif ($this->option('calling')) {
            return __DIR__ . '/stubs/facade_with_calling.stub';
        } elseif ($this->option('full')) {
            return __DIR__ . '/stubs/facade_full_function.stub';
        }

        // dd($this->hasArgument(['authorize']));
        return __DIR__ . '/stubs/facade_only.stub';
    }

    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     *
     */
    public function getStubVariables()
    {
        $className = $this->getClassName();

        return [
            'NAMESPACE' => 'App\\Facades',
            'CLASS_NAME' => $className,
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        $path = "app" . DIRECTORY_SEPARATOR . "Facades";
        $className = $this->getClassName();

        return base_path($path) . DIRECTORY_SEPARATOR . $className . '.php';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (! $this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }

    protected function getOptions()
    {
        return [
            ['authorize', 'a', InputOption::VALUE_NONE, 'Add authorize() and authorizeAndCalling() static methods'],
            ['calling', 'c', InputOption::VALUE_NONE, 'Add calling() and called() static methods for listen to facade events that use befor and after calling facade'],
            ['full', 'f', InputOption::VALUE_NONE, 'Add calling and auhtorization methods'],
        ];
    }
}
