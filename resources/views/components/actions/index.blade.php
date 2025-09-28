@php
    $value = $column->getValue($item);
    $actionName = $action->actionName ?? null;
    $actionString = $action->getActionString($item);
    $url = $action->getUrl($item);
    $hasAction = ! empty($actionName) || $action->actionClosure !== null;
    $hasUrl = ! empty($url);
    $requiresConfirmation = $action->requiresConfirmation;
    $modalContent = $requiresConfirmation ? $action->confirmationModalContent($item) : null;

    // Generate click handler for actions with confirmation
    $getActionClick = function () use ($requiresConfirmation, $actionString, $item, $modalContent) {
        if ($requiresConfirmation) {
            $modalData = json_encode($modalContent);
            return "showConfirmationModal(" . $modalData . ", '$item->id')";
        }
        return $actionString;
    };

    // Generate click handler for URL actions with confirmation
    $getUrlClick = function () use ($requiresConfirmation, $url, $modalContent) {
        if ($requiresConfirmation) {
            $modalData = json_encode($modalContent);
            return "showConfirmationModal(" . $modalData . ")";
        }
        return "window.location.href = '$url'";
    };

    // Determine the appropriate class based on action type and color
    $colorClass = match ($action->color) {
        "secondary" => $action->actionView === "badge" ? "bg-secondary" : "text-secondary",
        "success" => $action->actionView === "badge" ? "bg-success" : "text-success",
        "danger" => $action->actionView === "badge" ? "bg-danger" : "text-danger",
        "warning" => $action->actionView === "badge" ? "bg-warning" : "text-warning",
        "info" => $action->actionView === "badge" ? "bg-info" : "text-info",
        default => match ($action->key) {
            "delete" => $action->actionView === "badge" ? "bg-danger" : "text-danger",
            "edit" => $action->actionView === "badge" ? "bg-warning" : "text-warning",
            default => $action->actionView === "badge" ? "bg-primary" : "text-primary",
        },
    };

    // Button class for button style
    $btnClass = match ($action->color) {
        "secondary" => "btn-secondary",
        "success" => "btn-success",
        "danger" => "btn-danger",
        "warning" => "btn-warning",
        "info" => "btn-info",
        default => match ($action->key) {
            "delete" => "btn-danger",
            "edit" => "btn-warning",
            default => "btn-primary",
        },
    };
@endphp

<div
    x-data="{
        actionConfirmModal: false,
        modalData: {},
        currentItemId: null,
        showConfirmationModal(data, itemId = null) {
            this.modalData = data
            this.currentItemId = itemId
            this.actionConfirmModal = true
        },
        confirmModalAction() {
            if (this.modalData.hasAction) {
                if (this.modalData.actionString) {
                    // For closures, the actionString contains the complete function call
                    // We need to parse it and execute it safely
                    const actionCall = this.modalData.actionString
                    if (actionCall.includes('(')) {
                        const methodName = actionCall.split('(')[0]
                        const params = actionCall.match(/\(([^)]+)\)/)
                        if (params && params[1]) {
                            // Remove quotes and spaces from parameter
                            const paramValue = params[1].replace(/[\x27\x22 ]/g, '')
                            $wire.call(methodName, paramValue)
                        } else {
                            $wire.call(methodName)
                        }
                    } else {
                        $wire.call(actionCall)
                    }
                } else if (this.modalData.actionName) {
                    // Use actionName for regular actions
                    if (this.currentItemId) {
                        $wire.call(this.modalData.actionName, this.currentItemId)
                    } else {
                        $wire.call(this.modalData.actionName)
                    }
                }
            } else if (this.modalData.hasUrl && this.modalData.url) {
                window.location.href = this.modalData.url
            }

            this.actionConfirmModal = false
            this.modalData = {}
            this.currentItemId = null
        },
    }"
>
    @if ($action->actionView === "icon")
        @if ($hasAction)
            <button
                type="button"
                @if ($requiresConfirmation)
                    @click="{{ $getActionClick() }}"
                @else
                    wire:click="{{ $actionString }}"
                @endif
                @class([
                    "cursor-pointer",
                    $colorClass,
                ])
            >
                @if ($action->icon instanceof Htmlable)
                    <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                        {{ $action->icon }}
                    </span>
                @elseif (str_contains($action->icon, "/"))
                    <img
                        alt="{{ $action->label }}"
                        src="{{ $action->icon }}"
                        {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                    />
                @else
                    @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                @endif
            </button>
        @else
            @if ($requiresConfirmation)
                <button
                    type="button"
                    @click="{{ $getUrlClick() }}"
                    @class([
                        "cursor-pointer",
                        $colorClass,
                    ])
                >
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, "/"))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                        />
                    @else
                        @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                    @endif
                </button>
            @else
                <a href="{{ $url }}" @class([
                    $colorClass,
                ])>
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, "/"))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                        />
                    @else
                        @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                    @endif
                </a>
            @endif
        @endif
    @elseif ($action->actionView === "badge")
        @if ($hasAction)
            <button
                type="button"
                @if ($requiresConfirmation)
                    @click="{{ $getActionClick() }}"
                @else
                    wire:click="
                @if ($actionName)
                 {{ $actionName }}({{ $item->id }})
                @else
                 {{ $actionString }}
                @endif"
                @endif
                @class([
                    "badge cursor-pointer text-sm",
                    $colorClass,
                ])
            >
                {{ $action->label }}
            </button>
        @else
            @if ($requiresConfirmation)
                <button
                    type="button"
                    @click="{{ $getUrlClick() }}"
                    @class([
                        "badge cursor-pointer text-sm",
                        $colorClass,
                    ])
                >
                    {{ $action->label }}
                </button>
            @else
                <a
                    href="{{ $url }}"
                    @class([
                        "badge text-sm",
                        $colorClass,
                    ])
                >
                    {{ $action->label }}
                </a>
            @endif
        @endif
    @elseif ($action->actionView === "button")
        @if ($hasAction)
            <button
                type="button"
                @if ($requiresConfirmation)
                    @click="{{ $getActionClick() }}"
                @else
                    wire:click="
                @if ($actionName)
                 {{ $actionName }}({{ $item->id }})
                @else
                 {{ $actionString }}
                @endif"
                @endif
                @class([
                    "btn flex cursor-pointer justify-center px-1.5 py-1 !font-semibold",
                    $btnClass,
                ])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, "/"))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                        />
                    @else
                        @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </button>
        @else
            @if ($requiresConfirmation)
                <button
                    type="button"
                    @click="{{ $getUrlClick() }}"
                    @class([
                        "btn flex cursor-pointer justify-center px-1.5 py-1 !font-semibold",
                        $btnClass,
                    ])
                >
                    @if ($action->icon)
                        @if ($action->icon instanceof Htmlable)
                            <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                                {{ $action->icon }}
                            </span>
                        @elseif (str_contains($action->icon, "/"))
                            <img
                                alt="{{ $action->label }}"
                                src="{{ $action->icon }}"
                                {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                            />
                        @else
                            @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                        @endif
                    @endif

                    {{ $action->label }}
                </button>
            @else
                <a
                    href="{{ $url }}"
                    @class([
                        "btn flex justify-center px-1.5 py-1 !font-semibold",
                        $btnClass,
                    ])
                >
                    @if ($action->icon)
                        @if ($action->icon instanceof Htmlable)
                            <span {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}>
                                {{ $action->icon }}
                            </span>
                        @elseif (str_contains($action->icon, "/"))
                            <img
                                alt="{{ $action->label }}"
                                src="{{ $action->icon }}"
                                {{ $attributes->merge(["class" => ["ltr:mr-1.5 rtl:ml-1.5"]]) }}
                            />
                        @else
                            @svg($action->icon, "icon-column-item size-4 ltr:mr-1.5 rtl:ml-1.5", array_filter($attributes->getAttributes()))
                        @endif
                    @endif

                    {{ $action->label }}
                </a>
            @endif
        @endif
    @else
        @if ($hasAction)
            <button
                type="button"
                @if ($requiresConfirmation)
                    @click="{{ $getActionClick() }}"
                @else
                    wire:click="
                @if ($actionName)
                 {{ $actionName }}({{ $item->id }})
                @else
                 {{ $actionString }}
                @endif"
                @endif
                @class(["flex cursor-pointer items-center justify-start font-semibold ltr:mr-2 rtl:ml-2", $colorClass])
            >
                @if ($action->icon)
                    @if ($action->icon instanceof Htmlable)
                        <span {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}>
                            {{ $action->icon }}
                        </span>
                    @elseif (str_contains($action->icon, "/"))
                        <img
                            alt="{{ $action->label }}"
                            src="{{ $action->icon }}"
                            {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}
                        />
                    @else
                        @svg($action->icon, "icon-column-item size-4 ltr:mr-1 rtl:ml-1", array_filter($attributes->getAttributes()))
                    @endif
                @endif

                {{ $action->label }}
            </button>
        @else
            @if ($requiresConfirmation)
                <button
                    type="button"
                    @click="{{ $getUrlClick() }}"
                    @class(["flex cursor-pointer items-center justify-start font-semibold ltr:mr-2 rtl:ml-2", $colorClass])
                >
                    @if ($action->icon)
                        @if ($action->icon instanceof Htmlable)
                            <span {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}>
                                {{ $action->icon }}
                            </span>
                        @elseif (str_contains($action->icon, "/"))
                            <img
                                alt="{{ $action->label }}"
                                src="{{ $action->icon }}"
                                {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}
                            />
                        @else
                            @svg($action->icon, "icon-column-item size-4 ltr:mr-1 rtl:ml-1", array_filter($attributes->getAttributes()))
                        @endif
                    @endif

                    {{ $action->label }}
                </button>
            @else
                <a
                    href="{{ $url }}"
                    @class(["flex items-center justify-start font-semibold ltr:mr-2 rtl:ml-2", $colorClass])
                >
                    @if ($action->icon)
                        @if ($action->icon instanceof Htmlable)
                            <span {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}>
                                {{ $action->icon }}
                            </span>
                        @elseif (str_contains($action->icon, "/"))
                            <img
                                alt="{{ $action->label }}"
                                src="{{ $action->icon }}"
                                {{ $attributes->merge(["class" => ["ltr:mr-1 rtl:ml-1"]]) }}
                            />
                        @else
                            @svg($action->icon, "icon-column-item size-4 ltr:mr-1 rtl:ml-1", array_filter($attributes->getAttributes()))
                        @endif
                    @endif

                    {{ $action->label }}
                </a>
            @endif
        @endif
    @endif

    <!-- Confirmation Modal -->
    <x-laravel-simple-datatables-and-forms::actions.confirmation-modal />
</div>
