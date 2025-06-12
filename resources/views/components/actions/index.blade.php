@php
    $value = $column->getValue($item);
    $actionName = $action->actionName ?? null;
    $url = $action->getUrl($item);
    $hasAction = ! empty($actionName);
    $hasUrl = ! empty($url);

    // Determine the appropriate class based on action type and color
    $colorClass = match ($action->color) {
        'secondary' => $action->actionView === 'badge' ? 'bg-secondary' : 'text-secondary',
        'success' => $action->actionView === 'badge' ? 'bg-success' : 'text-success',
        'danger' => $action->actionView === 'badge' ? 'bg-danger' : 'text-danger',
        'warning' => $action->actionView === 'badge' ? 'bg-warning' : 'text-warning',
        'info' => $action->actionView === 'badge' ? 'bg-info' : 'text-info',
        default => match ($action->key) {
            'delete' => $action->actionView === 'badge' ? 'bg-danger' : 'text-danger',
            'edit' => $action->actionView === 'badge' ? 'bg-warning' : 'text-warning',
            default => $action->actionView === 'badge' ? 'bg-primary' : 'text-primary',
        },
    };

    // Button class for button style
    $btnClass = match ($action->color) {
        'secondary' => 'btn-secondary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
        default => match ($action->key) {
            'delete' => 'btn-danger',
            'edit' => 'btn-warning',
            default => 'btn-primary',
        },
    };
@endphp

<div>
    @if ($action->actionView === 'icon')
        @if ($hasAction)
            <button
                type="button"
                wire:click="{{ $actionName }}('{{ $item->id }}')"
                @class([
                    'cursor-pointer',
                    $colorClass,
                ])
            >
                @if ($action->icon instanceof Htmlable)
                    <span {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}>
                        {{ $action->icon }}
                    </span>
                @elseif (str_contains($action->icon, '/'))
                    <img
                        alt="{{ $action->label }}"
                        src="{{ $action->icon }}"
                        {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}
                    />
                @else
                    @svg($action->icon, 'icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5', array_filter($attributes->getAttributes()))
                @endif
            </button>
        @else
            <a href="{{ $url }}" @class([
                $colorClass,
            ])>
                @if ($action->icon instanceof Htmlable)
                    <span {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}>
                        {{ $action->icon }}
                    </span>
                @elseif (str_contains($action->icon, '/'))
                    <img
                        alt="{{ $action->label }}"
                        src="{{ $action->icon }}"
                        {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}
                    />
                @else
                    @svg($action->icon, 'icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5', array_filter($attributes->getAttributes()))
                @endif
            </a>
        @endif
    @elseif ($action->actionView === 'badge')
        @if ($hasAction)
            <button
                type="button"
                wire:click="{{ $actionName }}('{{ $item->id }}')"
                @class([
                    'badge cursor-pointer text-sm',
                    $colorClass,
                ])
            >
                {{ $action->label }}
            </button>
        @else
            <a
                href="{{ $url }}"
                @class([
                    'badge text-sm',
                    $colorClass,
                ])
            >
                {{ $action->label }}
            </a>
        @endif
    @elseif ($action->actionView === 'button')
        @if ($hasAction)
            <button
                type="button"
                wire:click="{{ $actionName }}('{{ $item->id }}')"
                @class([
                    'btn flex cursor-pointer justify-center px-1.5 py-1 !font-semibold',
                    $btnClass,
                ])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, '/'))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}
                        />
                    @else
                        @svg($action->icon, 'icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5', array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </button>
        @else
            <a
                href="{{ $url }}"
                @class([
                    'btn flex justify-center px-1.5 py-1 !font-semibold',
                    $btnClass,
                ])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, '/'))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(['class' => ['ltr:mr-1.5 rtl:ml-1.5']]) }}
                        />
                    @else
                        @svg($action->icon, 'icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5', array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </a>
        @endif
    @else
        @if ($hasAction)
            <button
                type="button"
                wire:click="{{ $actionName }}('{{ $item->id }}')"
                @class(['flex cursor-pointer items-center justify-start font-semibold ltr:mr-2 rtl:ml-2', $colorClass])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(['class' => ['ltr:mr-1 rtl:ml-1']]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, '/'))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(['class' => ['ltr:mr-1 rtl:ml-1']]) }}
                        />
                    @else
                        @svg($action->icon, 'icon-column-item size-4 ltr:mr-1 rtl:ml-1', array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </button>
        @else
            <a
                href="{{ $url }}"
                @class(['flex items-center justify-start font-semibold ltr:mr-2 rtl:ml-2', $colorClass])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(['class' => ['ltr:mr-1 rtl:ml-1']]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, '/'))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(['class' => ['ltr:mr-1 rtl:ml-1']]) }}
                        />
                    @else
                        @svg($action->icon, 'icon-column-item size-4 ltr:mr-1 rtl:ml-1', array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </a>
        @endif
    @endif
</div>
