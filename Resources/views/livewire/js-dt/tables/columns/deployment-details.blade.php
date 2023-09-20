@php
    /**
     * @var Modules\DataTable\Http\Livewire\DataTable\Base\BaseDataTable $this
     * @var Modules\KlaraDeployment\Models\Deployment $item
     * @var string $name
     * @var mixed $value
     **/

    $_commandTags = [];
    foreach ($item->enabledTasks as $_task) {
        foreach ($_task->command_list as $_command) {
            $_cmd = data_get($_command, 'cmd', '???');
            $_commandTags[] = $_cmd;
        }
    }

@endphp
{{--<div class="{{ $column['css_all'] }} {{ $column['css_body'] }}">--}}
<div class="">

    <div class="w-full">
        {{ $item->description }}
    </div>

    <div class="w-full text-secondary">
        Tasks: {{ $item->enabledTasks->count() }}/{{ $item->tasks->count() }}
    </div>

    <div class="w-full text-secondary">
        Total Commands:
        @foreach($_commandTags as $_commandTag)
            <span class="badge bg-secondary">{{ $_commandTag }}</span>
        @endforeach
    </div>

    {{--if can edit--}}
    @if($this->editable)

        {{-- all editable buttons --}}
        {{--        @include('data-table::livewire.js-dt.tables.columns.buttons.editable-buttons')--}}

    @endif
</div>
