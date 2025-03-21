@php
    use Milenmk\LaravelSimpleDatatables\Icons\IconManager;
    use Illuminate\Contracts\Support\Htmlable;
@endphp

@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
])

@php
    $icon = $alias ? IconManager::resolve($alias) : (null ?: $icon ?? $slot);
@endphp

@if ($icon instanceof Htmlable)
    <span {{ $attributes->class($class) }}>
        {{ $icon }}
    </span>
@elseif (str_contains($icon, '/'))
    <img {{
        $attributes
            ->merge(['src' => $icon])
            ->class($class)
    }} />
@else
    @svg($icon, $class, array_filter($attributes->getAttributes()))
@endif
