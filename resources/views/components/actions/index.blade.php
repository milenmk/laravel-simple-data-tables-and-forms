@php
    $value = $column->getValue($item);
@endphp

<div>
    @if ($action->actionView === 'icon')
        <a
            href="{{ $action->getUrl() }}"
            @class([
                match ($action->color) {
                    'secondary' => 'text-secondary',
                    'success' => 'text-success',
                    'danger' => 'text-danger',
                    'warning' => 'text-warning',
                    'info' => 'text-info',
                    default => match ($action->key) {
                        'delete' => 'text-danger',
                        'edit' => 'text-warning',
                        default => 'text-primary',
                    },
                },
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
        </a>
    @elseif ($action->actionView === 'badge')
        <a
            href="{{ $action->getUrl() }}"
            @class([
                'badge',
                match ($action->color) {
                    'secondary' => 'bg-secondary',
                    'success' => 'bg-success',
                    'danger' => 'bg-danger',
                    'warning' => 'bg-warning',
                    'info' => 'bg-info',
                    default => match ($action->key) {
                        'delete' => 'bg-danger',
                        'edit' => 'bg-warning',
                        default => 'bg-primary',
                    },
                },
            ])
        >
            {{ $action->label }}
        </a>
    @elseif ($action->actionView === 'button')
        <a
            href="{{ $action->getUrl() }}"
            @class([
                'btn flex justify-center',
                match ($action->color) {
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
                },
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
            {{ $action->label }}
        </a>
    @else
        <a
            href="{{ $action->getUrl() }}"
            @class([
                'flex items-start justify-start ltr:mr-2 rtl:ml-2',
                match ($action->color) {
                    'secondary' => 'text-secondary',
                    'success' => 'text-success',
                    'danger' => 'text-danger',
                    'warning' => 'text-warning',
                    'info' => 'text-info',
                    default => match ($action->key) {
                        'delete' => 'text-danger',
                        'edit' => 'text-warning',
                        default => 'text-primary',
                    },
                },
            ])
        >
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
            {{ $action->label }}
        </a>
    @endif
</div>
