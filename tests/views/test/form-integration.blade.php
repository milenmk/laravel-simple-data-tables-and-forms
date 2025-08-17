<div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        {!! $this->form !!}

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" wire:click="resetForm" class="btn btn-secondary">Reset</button>
        </div>
    </form>
</div>
