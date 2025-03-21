<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Milenmk\LaravelSimpleDatatables\Commands\Concerns\CanGenerateTables;
use Milenmk\LaravelSimpleDatatables\Commands\Concerns\CanIndentStrings;
use Milenmk\LaravelSimpleDatatables\Commands\Concerns\CanManipulateFiles;
use Milenmk\LaravelSimpleDatatables\Commands\Concerns\CanReadModelSchemas;

use function Laravel\Prompts\text;

class MakeTableCommand extends Command
{
    use CanGenerateTables;
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;

    protected $description = 'Create a new Livewire component containing a data table';

    protected $signature = 'make:milenmk-datatable {name?} {model?} {--G|generate}';

    public function handle(): int
    {
        $component = (string) str(
            $this->argument('name') ??
                text(label: 'What is the table name?', placeholder: 'Products/ListProducts', required: true),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) str($component)->afterLast('\\');
        $componentNamespace = str($component)->contains('\\') ? (string) str($component)->beforeLast('\\') : '';

        $view = str($component)
            ->replace('\\', '/')
            ->prepend('Livewire/')
            ->explode('/')
            ->map(fn ($segment) => Str::lower(Str::kebab($segment)))
            ->implode('.');

        $model = (string) str(
            $this->argument('model') ?? text(label: 'What is the model name?', placeholder: 'Product', required: true),
        )->replace('/', '\\');
        $modelClass = (string) str($model)->afterLast('\\');

        $path = (string) str($component)
            ->prepend('/')
            ->prepend(app_path('Livewire/'))
            ->replace('\\', '/')
            ->replace('//', '/')
            ->append('.php');

        $viewPath = resource_path(
            (string) str($view)
                ->replace('.', '/')
                ->prepend('views/')
                ->append('.blade.php'),
        );

        $this->copyStubToApp('Table', $path, [
            'class' => $componentClass,
            'columns' => $this->indentString(
                $this->option('generate') ? $this->getResourceTableColumns('App\\Models\\' . $model) : '//',
                4,
            ),
            'model' => $model,
            'modelClass' => $modelClass,
            'namespace' => 'App\\Livewire' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'view' => $view,
        ]);

        $this->copyStubToApp('TableView', $viewPath);

        $this->components->info("Datatable [{$path}] created successfully.");

        return static::SUCCESS;
    }
}
