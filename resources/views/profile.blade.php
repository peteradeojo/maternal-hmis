@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            <div class="card-header">Profile</div>
        </div>
        <div class="body py">
            <div class="row">
                <div class="col-4">
                    <b>Name: </b>{{ $user->name }}
                </div>
                <div class="col-4">
                    <b>Department:</b> {{ $user->department->name }}
                </div>
                <div class="col-4">
                    <b>Phone Number: </b> {{ $user->phone }}
                </div>
            </div>
            <hr class="my" />
            <form action="" method="post">
                @csrf
                <h3 class="h3">Change Password</h3>
                <div class="form-group">
                    <label for="pass">Password</label>
                    <input type="password" name="current_password" id="pass" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_pass">New Password</label>
                    <input type="password" name="password" id="new_pass" class="form-control" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-red">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
