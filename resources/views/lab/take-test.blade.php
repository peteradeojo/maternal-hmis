@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card foldable py px mb-2">
            <div class="header foldable-header">
                <p class="card-header">{{ $documentation->patient->name }}</p>
            </div>
            <div class="foldable-body body">
                <div class="py">
                    <p><b>Name: </b> {{ $documentation->patient->name }} </p>
                    <p><b>Card Number: </b> {{ $documentation->patient->card_number }} </p>
                    <p><b>Gender: </b> {{ $documentation->patient->gender_value }} </p>
                    <p><b>Age: </b> {{ $documentation->patient->dob?->diffInYears() }} </p>
                    <p><b>Visit Started: </b> {{ $documentation->visit->created_at->format('Y-m-d h:i A') }} </p>
                </div>
            </div>
        </div>
        <div class="card py px">
            <div class="header">
                <p class="card-header">Test Report</p>
            </div>
            <div class="body py">
                @foreach ($errors->all() as $message)
                    <p class="py px bg-red">{{ $message }}</p>
                @endforeach
                <form action="" method="POST">
                    @csrf
                    @foreach ($documentation->tests as $i => $test)
                        <table class="table-list mb-2" key="{{ $i }}">
                            <thead>
                                <tr class="test-description">
                                    <td colspan="3">Test: <b>{{ Str::upper($test->name) }}</b></td>
                                    <td colspan="2">
                                        <label for="test-done-{{ $i }}">
                                            <input type="checkbox" name="completed[{{ $i }}]"
                                                id="test-done-{{ $i }}"
                                                @if ($test->status == Status::completed->value)
                                                    checked disabled
                                                @endif
                                                >
                                            Mark Test Completed
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <th>Result</th>
                                    <th>Unit</th>
                                    <th>Reference Range</th>
                                    <td>
                                        @unless ($test->status == Status::completed->value)
                                            <button class="add-result btn bg-blue-700 text-white" type="button">Add Result</button>
                                        @endunless
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($test->results ?? [] as $j => $result)
                                    <tr>
                                        <td><input type="text"
                                                name="description[{{ $i }}][{{ $j }}]"
                                                class="form-control" value="{{ $result->description }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text" name="result[{{ $i }}][{{ $j }}]"
                                                class="form-control" value="{{ $result->result }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text" name="unit[{{ $i }}][{{ $j }}]"
                                                class="form-control" value="{{ $result->unit }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text"
                                                name="reference_range[{{ $i }}][{{ $j }}]"
                                                class="form-control" value="{{ $result->reference_range }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td>
                                            @unless ($test->status == Status::completed->value)
                                                <button class="remove-result" type="button">Remove</button>
                                            @endunless
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- @unless ($test->status == Status::completed->value)
                        @endunless --}}
                    @endforeach

                    <div class="form-group">
                        <label for="comment">Lab Comment</label>
                        <textarea name="comment" id="comment" class="form-control"></textarea>
                    </div>
                    @unless ($documentation->all_tests_completed)
                        <div class="form-group">
                            <button class="btn btn-blue" type="submit">Submit</button>
                        </div>
                    @endunless
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $(".add-result").on('click', function() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const key = table.getAttribute('key');
                const rowCount = tbody.querySelectorAll('tr').length;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" name="description[${key}][${rowCount}]" class="form-control"></td>
                    <td><input type="text" name="result[${key}][${rowCount}]" class="form-control"></td>
                    <td><input type="text" name="unit[${key}][${rowCount}]" class="form-control"></td>
                    <td><input type="text" name="reference_range[${key}][${rowCount}]" class="form-control"></td>
                    <td><button class="remove-result btn bg-red-500 text-white" type="button">Remove</button></td>
                `;

                tbody.appendChild(tr);

                $(".remove-result").on('click', function() {
                    this.closest('tr').remove();
                });
            });

            $(".remove-result").on('click', function() {
                this.closest('tr').remove();
            });
        });
    </script>
@endpush
