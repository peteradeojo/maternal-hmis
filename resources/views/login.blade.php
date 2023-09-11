@extends('layouts.auth')

@section('content')
    <div class="container" id="login-container">
        <div class="d-flex">
            <div class="col-6 d-flex align-items-center">
                <img src="{{ asset('favicon-3.png') }}">
            </div>
            <div class="col-6">
                @if(session('error'))
                    <div class="bg-red py px" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <h1>Login</h1>
                <form action="{{ route('login') }}" id="login-form" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone') }}" required autofocus>

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required>

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
@endsection
