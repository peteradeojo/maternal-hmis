@extends('layouts.auth')

@section('content')
    <div class="w-full sm:w-1/2 px-16 py-8 bg-green-400">
        <div class="flex align-items-center">
            <img src="{{ asset('favicon-3.png') }}">
        </div>
        <div class="w-full">
            @if (session('error'))
                <div class="bg-red py px" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('login') }}" class="grid gap-y-3" method="post">
                @csrf
                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" autofocus class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone') }}" required autofocus>

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required>

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="p-2 px-4 text-lg bg-green-700 text-white">Login <i class="fa fa-sign-in"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
