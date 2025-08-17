<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Components;

use Illuminate\Support\Facades\View;
use Livewire\Component;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\CheckboxField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\InputField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\SelectField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\TextareaField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields\ToggleField;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Form;
use Milenmk\LaravelSimpleDatatablesAndForms\Form\Sections\Section;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;
use Milenmk\LaravelSimpleDatatablesAndForms\Traits\HasForm;

class TestFormComponent extends Component
{
    use HasForm;

    public function mount(): void
    {
        $this->mountForm();
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(TestModel::class)
            ->heading([
                'title' => 'Test Form',
                'description' => 'A test form for validation',
            ])
            ->columns(2)
            ->schema([
                InputField::make('name')
                    ->label('Full Name')
                    ->placeholder('Enter your full name')
                    ->required()
                    ->maxLength(255),

                InputField::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->placeholder('Enter your email'),

                SelectField::make('category')
                    ->label('Category')
                    ->options([
                        'Electronics' => 'Electronics',
                        'Clothing' => 'Clothing',
                        'Books' => 'Books',
                        'Food' => 'Food',
                        'Other' => 'Other',
                    ])
                    ->emptyOption('Select a category')
                    ->required(),

                CheckboxField::make('is_active')
                    ->label('Active Status')
                    ->default(true),

                ToggleField::make('notifications')
                    ->label('Enable Notifications')
                    ->onLabel('Yes')
                    ->offLabel('No')
                    ->default(false),

                InputField::make('price')
                    ->label('Price')
                    ->number()
                    ->min(0)
                    ->step(0.01)
                    ->placeholder('0.00'),

                TextareaField::make('description')
                    ->label('Description')
                    ->placeholder('Enter a description...')
                    ->rows(4)
                    ->columnSpan('full'),
            ]);
    }

    public function formWithSections(Form $form): Form
    {
        return $form->model(TestModel::class)->sections([
            Section::make('personal_info')
                ->label('Personal Information')
                ->description('Basic personal details')
                ->icon('user')
                ->columns(2)
                ->fields([
                    InputField::make('name')
                        ->label('Full Name')
                        ->required(),
                    InputField::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required(),
                ]),

            Section::make('preferences')
                ->label('Preferences')
                ->description('Configure your preferences')
                ->collapsible()
                ->fields([
                    ToggleField::make('notifications')->label('Email Notifications'),
                    SelectField::make('category')
                        ->label('Preferred Category')
                        ->options([
                            'Electronics' => 'Electronics',
                            'Clothing' => 'Clothing',
                            'Books' => 'Books',
                        ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $this->validate($this->getValidationRulesFromForm());

        // Create or update model
        if ($this->formModel) {
            $this->formModel->update($this->formData);
        } else {
            TestModel::create($this->formData);
        }

        session()->flash('message', 'Form saved successfully!');
        $this->resetForm();
    }

    public function render()
    {
        return View::file(__DIR__ . '/../views/test/form-integration.blade.php');
    }
}
