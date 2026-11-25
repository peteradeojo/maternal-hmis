@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Diagnoses Report</div>

        <form method="POST">
            @csrf
            <div class="flex gap-x-4">
                <div class="form-group flex flex-col flex-grow">
                    <label>Search query<span class="text-red-500">*</span></label>
                    <input placeholder="Search for diagnosis" type="text" class="form-control " required="required"
                        name="search" value="{{ $form['search'] ?? '' }}" />
                </div>
                <div class="form-group flex-grow">
                    <label>From <span class="text-red-500">*</span></label>
                    <input type="date" name="from" class="form-control" max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                        value="{{ $form['from'] ?? '' }}" />
                </div>
                <div class="form-group flex-grow">
                    <label>To</label>
                    <input type="date" name="to" class="form-control" max="{{ date('Y-m-d') }}"
                        value="{{ $form['to'] ?? '' }}" />
                </div>
                <div class="form-group flex-grow">
                    <label>Interval<span class="text-red-500">*</span></label>
                    <select name="interval" class="form-control">
                        <option value="1 month">Monthly</option>
                        <option value="1 week">Weekly</option>
                        <option value="1 day">Daily</option>
                    </select>
                </div>
            </div>
            <div class="form-group w-full">
                <button class="btn bg-primary">Submit</button>
            </div>
        </form>
    </div>

    @isset($data)
        <div class="card">
            <table class="table">
                <tbody>
                    @foreach ($data as $entry)
                        <tr>
                            <td>{{ $entry->month }}</td>
                            <td>{{ $entry->num }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endisset
@endsection
