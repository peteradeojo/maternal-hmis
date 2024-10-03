<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div>
        <div class="flex justify-between py-2">
            <input type="search" class="input" wire:model.debounce.500ms="searchTerm" placeholder="Search" wire:keydown.debounce="search" />
        </div>

        <table class="table">
            <thead>
                <tr>
                    @foreach ($headers as $f)
                        <th>{{ $f }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    @php
                        $render = null;
                    @endphp
                    <tr>
                        @foreach ($displayFields as $i => $field)
                            <td>
                                @if (is_array($field))
                                    @php
                                        @[$field, $render] = $field;
                                    @endphp
                                @endif

                                @if (str_contains($field, '.'))
                                    @php
                                        $data = explode('.', $field);
                                        $rowData = $row;
                                        foreach ($data as $item) {
                                            $rowData = $rowData->$item;
                                        }
                                        // echo $rowData;
                                    @endphp
                                @else
                                    @php
                                        $rowData = $row->$field;
                                    @endphp
                                @endif

                                {{ resolve_render($rowData, $render) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
