@php
    use Illuminate\Support\Collection;
    use Milenmk\LaravelSimpleDatatablesAndForms\Table\Columns\IconColumn\IconColumnSize;

    $size = $column->size ?? IconColumnSize::Large;
    $state = $column->getValue($item);
@endphp

<td
    @class([
        'items-center px-2 py-1.5',
        $column->backgroundColor => $column->backgroundColor ?: '',
        'text-wrap' => $column->wrap,
        'text-nowrap' => ! $column->wrap,
    ])
    style="text-align: {{ $column->align }}"
    id="td-{{ $column->key }}"
    data-column="{{ $column->key }}"
>
    @php
        $color = $column->getColor($state) ?? 'gray';
    @endphp

    <div
        @class([
            'inline-flex w-full',
            'justify-center' => $column->align === 'center',
            'justify-end' => $column->align === 'right',
        ])
    >
        <x-laravel-simple-datatables-and-forms::icon
            :icon="$column->getIcon($state)"
            @class([
                'icon-column-item',
                match ($size) {
                    IconColumnSize::ExtraSmall, 'xs' => 'h-3 w-3',
                    IconColumnSize::Small, 'sm' => 'h-4 w-4',
                    IconColumnSize::Medium, 'md' => 'h-5 w-5',
                    IconColumnSize::Large, 'lg' => 'h-6 w-6',
                    IconColumnSize::ExtraLarge, 'xl' => 'h-7 w-7',
                    IconColumnSize::TwoExtraLarge, '2xl' => 'h-8 w-8',
                    default => $size,
                },
                match ($color) {
                    'secondary' => 'text-secondary',
                    'success' => 'text-success',
                    'danger' => 'text-danger',
                    'warning' => 'text-warning',
                    'info' => 'text-info',
                    default => 'text-primary',
                },
            ])
        />
    </div>
</td>
