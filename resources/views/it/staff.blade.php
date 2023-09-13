@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Staff</h1>
        <div class="card py px mb-1 foldable">
            <div class="foldable-header">
                <h2>New Staff</h2>
            </div>

            <div class="foldable-body unfolded">
                @foreach ($errors->all() as $message)
                    <p>{{ $message }}</p>
                @endforeach
                <form action="" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="name">Department</label>
                        <select name="department_id" id="department" class="form-control" required>
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">First Name</label>
                        <input type="text" name="firstname" id="name" class="form-control" placeholder="First Name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="name">Last Name</label>
                        <input type="text" name="lastname" id="name" class="form-control" placeholder="Last Name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="name">Phone Number</label>
                        <input type="text" name="phone" id="name" class="form-control" placeholder="Phone Number"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" name="password" id="password" class="form-control" placeholder="Password"
                            required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-red">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Staff Table --}}
        <div class="card py px">
            <div class="body">
                <table id="staff-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Department</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->department->name }}</td>
                                <td><a href="{{ route('it.staff.view', $user->id) }}">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $("#staff-table").DataTable();
    </script>
@endpush
