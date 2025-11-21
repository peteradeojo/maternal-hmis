@extends('layouts.app')
@section('title', 'Staff Profile :: ' . $user->name)

@section('content')
    <div class="card py px">
        <div class="header">
            <div class="card-header">{{ $user->name }}</div>
        </div>
        <div class="body py-1">
            <div class="row">
                <div class="col-6">
                    <p><strong>Department:</strong> {{ $user->department->name }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Created:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
                    <p><strong>Updated:</strong> {{ $user->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>

            <form action="" method="post" class="mt-2">
                @csrf
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" class="form-control">
                        @foreach ($departments as $option)
                            <option value="{{ $option->id }}" @match($option->id, $user->department_id) selected="true" @endmatch>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Change Password" />
                </div>
                <button type="submit" class="btn btn-blue">Submit</button>
            </form>
        </div>
    </div>
@endsection
