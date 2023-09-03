@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px">
            <h2 class="mb-1">Antenatal Registration Form</h2>

            <form action="" method="post">
                @csrf
                <div class="mb-2">
                    <h2>Biodata</h2>
                    <div class="form-group">
                        <select name="category_id" class="form-control" readonly>
                            <option value="{{ $ancCategory->id }}" selected>Antenatal</option>
                        </select>
                    </div>
                    @include('records.components.patient-form-basic')
                </div>
                <div class="mb-2">
                    <h2>Antenatal Info</h2>
                    {{-- @include('records.components.lmp-form') --}}
                    @livewire('lmp-form')
                </div>
                <div class="mb-2">
                    <h2>Spouse</h2>
                    @include('records.components.spouse-form')
                </div>
                <div class="mb-2">
                    <h2>Next of Kin</h2>
                    @include('records.components.next-of-kin-form')
                </div>
                <div class="form-group">
                    <button class="btn btn-blue">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
