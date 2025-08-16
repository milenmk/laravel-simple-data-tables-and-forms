@php
    use Milenmk\LaravelSimpleDatatablesAndForms\Icons\IconManager;
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
    <img alt="{{ $icon ?? 'icon' }}" src="{{ $icon }}" {{ $attributes->merge(['class' => $class]) }} />
@else
    @svg($icon, $class, array_filter($attributes->getAttributes()))
@endif
