<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\Concerns\CanGenerateForms;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\Concerns\CanIndentStrings;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\Concerns\CanManipulateFiles;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\Concerns\CanReadModelSchemas;

use function Laravel\Prompts\text;

class MakeFormCommand extends Command
{
    use CanGenerateForms;
    use CanIndentStrings;
    use CanManipulateFiles;
    use CanReadModelSchemas;

    protected $description = 'Create a new Livewire component containing a form';

    protected $signature = 'make:milenmk-form {name?} {type?} {model?} {--G|generate}';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $component = (string) str(
            $this->argument('name') ??
                text(label: 'What is the form name?', placeholder: 'Products/CreateProduct', required: true),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $componentClass = (string) str($component)->afterLast('\\');
        $componentNamespace = str($component)->contains('\\') ? (string) str($component)->beforeLast('\\') : '';

        $type = (string) str(
            $this->argument('type') ??
                text(label: 'What type of form?', placeholder: 'create', default: 'create', required: true),
        )->lower();

        // Validate type
        if (! in_array($type, ['create', 'edit'])) {
            $this->components->error("Invalid form type '{$type}'. Must be 'create' or 'edit'.");

            return static::FAILURE;
        }

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

        // Determine stub names based on type
        $formStub = $type === 'create' ? 'CreateForm' : 'EditForm';
        $viewStub = $type === 'create' ? 'CreateFormView' : 'EditFormView';

        // Generate route parameter for edit forms
        $routeParameter = $type === 'edit' ? Str::lower($modelClass) : '';

        $this->copyStubToApp($formStub, $path, [
            'class' => $componentClass,
            'fields' => $this->indentString(
                $this->option('generate') ? $this->getResourceFormFields('App\\Models\\' . $model) : '//',
                4,
            ),
            'model' => $model,
            'modelClass' => $modelClass,
            'namespace' => 'App\\Livewire' . ($componentNamespace !== '' ? "\\{$componentNamespace}" : ''),
            'view' => $view,
            'routeParameter' => $routeParameter,
        ]);

        $this->copyStubToApp($viewStub, $viewPath, [
            'type' => $type,
        ]);

        $this->components->info("Form [{$path}] created successfully.");

        if ($type === 'edit') {
            $this->components->info("Don't forget to add the route parameter to your component mount method!");
            $this->components->info("Example route: Route::get('/{$routeParameter}/{id}/edit', YourComponent::class);");
        }

        return static::SUCCESS;
    }
}
