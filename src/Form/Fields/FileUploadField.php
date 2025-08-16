<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Form\Fields;

use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class FileUploadField extends Field
{
    public bool $multiple = false;
    public ?string $accept = null;
    public ?int $maxSize = null; // In KB
    public int $maxFiles = 1;
    public array $acceptedFileTypes = [];
    public string $directory = 'uploads';
    public string $visibility = 'private';
    public bool $imagePreview = false;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->type = 'file';
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        if ($multiple && $this->maxFiles === 1) {
            $this->maxFiles = 5; // Default for multiple
        }

        return $this;
    }

    public function accept(string $accept): static
    {
        $this->accept = $accept;

        return $this;
    }

    public function maxSize(int $maxSize): static
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    public function maxFiles(int $maxFiles): static
    {
        $this->maxFiles = $maxFiles;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    public function visibility(string $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function image(bool $imagePreview = true): static
    {
        $this->imagePreview = $imagePreview;
        $this->acceptedFileTypes(['jpg', 'jpeg', 'png', 'gif']);

        return $this;
    }

    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        // Set accepted MIME type attribute based on file types
        $mimeTypes = [];
        foreach ($types as $type) {
            $mimeTypes[] = match (strtolower($type)) {
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'txt' => 'text/plain',
                default => ".{$type}",
            };
        }

        $this->accept = implode(',', $mimeTypes);

        return $this;
    }

    public function getValidationRules(): array
    {
        $rules = parent::getValidationRules();

        if ($this->multiple) {
            $rules[] = 'array';

            if ($this->maxFiles > 1) {
                $rules[] = "max:{$this->maxFiles}";
            }

            $fileRules = ['file'];

            if ($this->maxSize) {
                $fileRules[] = "max:{$this->maxSize}";
            }

            if (! empty($this->acceptedFileTypes)) {
                $fileRules[] = 'mimes:' . implode(',', $this->acceptedFileTypes);
            }

            $rules["{$this->name}.*"] = $fileRules;
        } else {
            $rules[] = 'file';

            if ($this->maxSize) {
                $rules[] = "max:{$this->maxSize}";
            }

            if (! empty($this->acceptedFileTypes)) {
                $rules[] = 'mimes:' . implode(',', $this->acceptedFileTypes);
            }
        }

        return $rules;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laravel-simple-datatables-and-forms::components.form.fields.file-upload', [
            'field' => $this,
            'errors' => session()->get('errors', new ViewErrorBag),
        ])->render();
    }
}
