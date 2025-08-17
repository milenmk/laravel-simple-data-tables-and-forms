<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Table\Actions;

use Closure;
use Illuminate\Support\HtmlString;

class BaseAction
{
    public string $key;

    public string|array|null $label;

    public mixed $url = null;
    public ?string $textColor = null;
    public ?string $color = null;
    public ?string $buttonBackground = null;
    public ?string $actionView = null;
    public string|bool|Closure|null $icon = null;
    public ?string $actionName = null;
    public ?Closure $actionClosure = null;

    public bool $requiresConfirmation = false;
    public string|HtmlString|Closure|null $modalHeading = null;
    public string|HtmlString|Closure|null $modalDescription = null;
    public string|HtmlString|Closure|null $modalContent = null;
    public string|Closure|null $modalIcon = null;
    public string|HtmlString|Closure|null $modalConfirmationButtonLabel = null;

    protected string $view = 'laravel-simple-datatables-and-forms::components.actions.index';

    public function __construct($key)
    {
        $this->key = $key;
        $this->label = $key;
    }

    public static function make($key): static
    {
        return new static($key);
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function button(): self
    {
        $this->actionView = 'button';

        return $this;
    }

    public function iconButton(): self
    {
        $this->actionView = 'icon';

        return $this;
    }

    public function badge(): self
    {
        $this->actionView = 'badge';

        return $this;
    }

    public function hiddenIcon(): static
    {
        $this->icon = null;

        return $this;
    }

    public function icon(string|bool|Closure|null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(mixed $value): self
    {
        $this->url ??= $value; // Only set if null

        return $this;
    }

    /**
     * @param  string  $color  Full value e.g. text-gray-500, text-primary
     * @return $this
     */
    public function color(string $color): self
    {
        $this->textColor = $color;
        $this->color = str_replace('text-', '', $color);

        return $this;
    }

    /**
     * @param  string  $buttonBackground  Color value e.g. gray-500, primary
     * @return $this
     */
    public function buttonBackground(string $buttonBackground): self
    {
        $this->buttonBackground = $buttonBackground;

        return $this;
    }

    public function action(string|Closure $action): self
    {
        if ($action instanceof Closure) {
            $this->actionClosure = $action;
        } else {
            $this->actionName = $action;
        }

        return $this;
    }

    public function requiresConfirmation(bool $value = true): static
    {
        if ($value === true) {
            $this->requiresConfirmation = true;
        }

        return $this;
    }

    public function modalHeading(string|HtmlString|Closure $heading): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalDescription(string|HtmlString|Closure $description): static
    {
        $this->modalDescription = $description;

        return $this;
    }

    public function modalContent(string|HtmlString|Closure $content): static
    {
        $this->modalContent = $content;

        return $this;
    }

    public function modalIcon(string|Closure $icon): static
    {
        $this->modalIcon = $icon;

        return $this;
    }

    public function modalConfirmationButtonLabel(string|HtmlString|Closure $label): static
    {
        $this->modalConfirmationButtonLabel = $label;

        return $this;
    }

    public function confirmationModalContent($record = null): array
    {
        $heading = $this->getModalHeading($record) ?? 'Confirm Action';
        $description = $this->getModalDescription($record) ?? 'Are you sure you want to perform this action?';
        $content = $this->getModalContent($record);
        $confirmationButtonLabel = $this->getModalConfirmationButtonLabel($record);

        return [
            'heading' => $heading instanceof HtmlString ? $heading->toHtml() : $heading,
            'description' => $description instanceof HtmlString ? $description->toHtml() : $description,
            'content' => $content instanceof HtmlString ? $content->toHtml() : $content,
            'confirmationButtonLabel' => $confirmationButtonLabel instanceof HtmlString
                    ? $confirmationButtonLabel->toHtml()
                    : $confirmationButtonLabel,
            'icon' => $this->getModalIcon($record),
            'hasUrl' => $this->hasUrl(),
            'hasAction' => $this->hasAction(),
            'url' => $this->getUrl($record),
            'actionName' => $this->actionName,
            'actionString' => $this->getActionString($record),
        ];
    }

    public function getModalHeading($record = null): string|HtmlString|null
    {
        return match (true) {
            is_callable($this->modalHeading) && $record !== null => call_user_func($this->modalHeading, $record),
            is_callable($this->modalHeading) => call_user_func($this->modalHeading),
            default => $this->modalHeading,
        };
    }

    public function getModalDescription($record = null): string|HtmlString|null
    {
        return match (true) {
            is_callable($this->modalDescription) && $record !== null => call_user_func(
                $this->modalDescription,
                $record,
            ),
            is_callable($this->modalDescription) => call_user_func($this->modalDescription),
            default => $this->modalDescription,
        };
    }

    public function getModalContent($record = null): string|HtmlString|null
    {
        return match (true) {
            is_callable($this->modalContent) && $record !== null => call_user_func($this->modalContent, $record),
            is_callable($this->modalContent) => call_user_func($this->modalContent),
            default => $this->modalContent,
        };
    }

    public function getModalIcon($record = null): ?string
    {
        return match (true) {
            is_callable($this->modalIcon) && $record !== null => call_user_func($this->modalIcon, $record),
            is_callable($this->modalIcon) => call_user_func($this->modalIcon),
            default => $this->modalIcon,
        };
    }

    public function getModalConfirmationButtonLabel($record = null): string|HtmlString|null
    {
        return match (true) {
            is_callable($this->modalConfirmationButtonLabel) && $record !== null => call_user_func(
                $this->modalConfirmationButtonLabel,
                $record,
            ),
            is_callable($this->modalConfirmationButtonLabel) => call_user_func($this->modalConfirmationButtonLabel),
            default => $this->modalConfirmationButtonLabel,
        };
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }

    public function hasAction(): bool
    {
        return $this->actionName !== null || $this->actionClosure !== null;
    }

    public function getUrl($record = null): mixed
    {
        return match (true) {
            is_callable($this->url) && $record !== null => call_user_func($this->url, $record),
            is_callable($this->url) => call_user_func($this->url),
            default => $this->url ?? null,
        };
    }

    /**
     * Get the action string to be used in wire:click or @click attributes
     * For closures, it returns the result of executing the closure with the record
     * For action names, it returns the action name with the record ID
     */
    public function getActionString($record = null): ?string
    {
        if ($this->actionClosure) {
            $result = $this->executeAction($record);

            return is_string($result) ? $result : null;
        }

        if ($this->actionName) {
            return $this->actionName;
        }

        return null;
    }

    public function executeAction($record = null): mixed
    {
        if ($this->actionClosure) {
            return call_user_func($this->actionClosure, $record);
        }

        return null;
    }

    public function label(string|array|null $value): self
    {
        $this->label = $value;

        return $this;
    }
}
