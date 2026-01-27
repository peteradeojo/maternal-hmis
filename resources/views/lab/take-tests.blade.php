@extends('layouts.app')

@section('content')
    <x-back-link />
    <div class="container">
        <div class="bg-white p-1">
            <div class="body">
                <x-patient-profile :patient="$patient" />
            </div>
        </div>
    </div>

    <div class="container">
        <div class="bg-white p-4 mt-2">
            <div class="grid gap-y-2">
                @forelse ($tests as $i => $test)
                    <form method="post" data-key="{{ $test->id }}" class="test-form">
                        <p>Date ordered: {{ $test->created_at }}</p>
                        <table class="table table-list mb-2" key="{{ $test->id }}">
                            <thead>
                                <tr class="test-description">
                                    <td colspan="3">Test: <b>{{ Str::upper($test->name) }}</b></td>
                                    <td colspan="2">
                                        {{-- <label for="test-done-{{ $i }}">
                                            <input type="checkbox" name="completed[{{ $i }}]"
                                                id="test-done-{{ $i }}"
                                                @if ($test->status == Status::completed->value) checked disabled @endif>
                                            Mark Test Completed
                                        </label> --}}
                                        <select @match(Status::closed->value, $test->status) readonly="readonly" @endmatch
                                            name="status" class="w-1/2 p-0.5 text-sm">
                                            <option @match(Status::pending->value, $test->status) selected="selected"
                                                @endmatch value="{{ Status::pending->value }}">Pending
                                            </option>
                                            <option @match(Status::active->value, $test->status) selected="selected"
                                                @endmatch value="{{ Status::active->value }}">Sample
                                                Collected</option>
                                            <option @match(Status::completed->value, $test->status) selected="selected"
                                                @endmatch value="{{ Status::completed->value }}">Tests
                                                Done</option>
                                            <option @match(Status::closed->value, $test->status) selected="selected"
                                                @endmatch value="{{ Status::closed->value }}">Delivered
                                            </option>
                                            <option @match(Status::cancelled->value, $test->status) selected="selected"
                                                @endmatch value="{{ Status::cancelled->value }}">Rejected
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                @unless ($test->status == Status::closed->value)
                                    <tr>
                                        <th>Description</th>
                                        <th>Result</th>
                                        <th>Unit</th>
                                        <th>Reference Range</th>
                                        <td>
                                            <button class="add-result btn bg-blue-700 text-white" type="button">Add
                                                Result</button>
                                        </td>
                                    </tr>
                                @endunless
                            </thead>
                            <tbody>
                                @foreach ($test->results ?? [] as $j => $result)
                                    <tr>
                                        <td><input type="text" name="description" class="form-control"
                                                value="{{ $result->description }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text" name="result" class="form-control"
                                                value="{{ $result->result }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text" name="unit" class="form-control"
                                                value="{{ $result->unit }}"
                                                @if ($test->status == Status::completed->value) disabled @endif></td>
                                        <td><input type="text" name="reference_range" class="form-control"
                                                value="{{ $result->reference_range }}"
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
                        @unless (Status::closed->value == $test->status)
                            <button type="submit" class="btn btn-green">Submit</button>
                        @endunless
                    </form>
                @empty
                    <p>No pending tests for this patient.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            function addResultRow() {
                const table = this.closest('table');
                const tbody = table.querySelector('tbody');
                const key = table.getAttribute('key');
                const rowCount = tbody.querySelectorAll('tr').length;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" name="description" class="form-control" required /></td>
                    <td><input type="text" name="result" class="form-control" /></td>
                    <td><input type="text" name="unit" class="form-control" /></td>
                    <td><input type="text" name="reference_range" class="form-control" /></td>
                    <td><button class="remove-result btn bg-red-500 text-white" type="button">Remove</button></td>
                `;

                tbody.appendChild(tr);

                $(".remove-result").on('click', function() {
                    this.closest('tr').remove();
                });
            }

            $(".add-result").on('click', addResultRow);

            $(".remove-result").on('click', function() {
                this.closest('tr').remove();
            });

            $(".test-form").on('submit', (e) => {
                e.preventDefault();
                console.log(e);
                const form = e.target;
                const {
                    key
                } = form.dataset;

                const rows = $(form).find("tbody tr");
                const status = $(form).find("thead select").val(); //.is(":checked");
                const submitter = $(form).find("button[type='submit']");

                $(submitter).prop('disabled', true);

                const data = {
                    results: [],
                    status
                };
                // if (rows.length < 1) {
                //     alert("No result added. Add at least one result to submit.");
                //     return;
                // }

                rows.each((_, r) => {
                    const inputs = $(r).find("input");
                    const rData = {};

                    inputs.each((_, i) => {
                        const v = $(i);
                        rData[v.attr('name')] = v.val();
                    });

                    data.results.push(rData);
                });

                axios.get('/sanctum/csrf-cookie').then(() => {
                    // return;
                    axios.post(`/api/laboratory/save-test/${key}`, data).then((response) => {
                        console.log(response.data);

                        const p = document.createElement('p');
                        p.classList.add("text-green-500", 'text-sm', 'pt-3',
                            'font-semibold');
                        p.innerText = "Test results saved!";

                        form.appendChild(p);

                        setTimeout(() => {
                            p.remove()
                        }, 3000);
                    }).catch(err => {
                        err.data;
                        const p = document.createElement('p');
                        p.classList.add("text-red-500", 'text-sm', 'pt-3');
                        p.innerText = "An error occurred.";

                        form.appendChild(p);

                        setTimeout(() => {
                            p.remove()
                        }, 5000);
                    }).finally(() => {
                        $(submitter).prop('disabled', false);
                    })
                });
            });
        });
    </script>
@endpush
