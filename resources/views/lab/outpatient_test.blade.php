@extends('layouts.app')
@section('title', 'Out-Patient Testing')

@section('content')
    <div class="container bg-white p-4">
        <h2 class="header">Out-Patient</h2>
        <hr />

        <div class="p-2">
            <form method="POST" action="" id="outpatient_form">
                @csrf
                <fieldset class="border p-2">
                    <legend>Patient Biodata</legend>
                    <div class="form-group">
                        <label>Name</label>
                        <x-input-text name="name" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <x-input-select name="gender" required>
                            <option disabled selected="selected">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </x-input-select>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <x-input-text name="phone" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>E-mail Address</label>
                        <x-input-text name="email" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <x-input-text name="address" class="form-control" />
                    </div>
                </fieldset>
                <fieldset class="border p-2">
                    <legend>Test</legend>

                    <div x-data="{ tests: [] }" x-on:selected.window="tests.push($event.detail)">
                        <livewire:dynamic-product-search />

                        <template x-for="test in tests">
                            <div class="flex-center gap-x-4">
                                <p x-text="test.product.name"></p>
                                <input type="hidden" name="tests[]" x-bind:value="test.product.id">
                                <button type="button" @click="tests.splice(tests.indexOf(test), 1)"
                                    class="btn btn-sm bg-red-500 text-white">&times;</button>
                            </div>
                        </template>
                    </div>
                </fieldset>

                <div class="form-group">
                    <button class="btn bg-green-500 text-white">Submit <i class="fa fa-check"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#outpatient_form').submit(function(e) {
                e.preventDefault();

                const data = new FormData(e.target);

                axios.post("{{ route('lab.outpatient_test') }}", data)
                    .then(function(response) {
                        console.log(response.data);
                        notifySuccess("Success");
                        // e.target.reset();

                        // location.href = '/';
                    })
                    .catch(function(error) {
                        console.error(error);
                        notifyError(error.message);
                    });
            });
        });
    </script>
@endpush
