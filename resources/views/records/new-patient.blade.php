@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px">
            <h2 class="mb-1">Registration Form</h2>

            <form action="" method="post">
                @csrf
                {{-- <div class="form-group">
                    <label for="category">Category *</label>
                    <select name="category_id" id="category" class="form-control" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div> --}}

                <div class="mb-2">
                    <h2>Biodata</h2>
                    @include('records.components.patient-form-basic')
                </div>
                <div class="mb-2">
                    <h2>Next of Kin</h2>
                    @include('records.components.next-of-kin-form')
                </div>
                <div class="py">
                    <h2>Health Insurance</h2>
                    @include('records.components.hmi-form')
                </div>

                <div class="form-group">
                    <button class="btn btn-blue">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
