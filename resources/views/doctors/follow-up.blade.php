@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card py px foldable">
            <div class="header foldable-header">
                <p>Follow Up on: {{ $documentation->patient->name }}</p>
            </div>
            <div class="body foldable-body unfolded">
                <div class="py">
                    <div class="row">
                        <div class="col-6">
                            <h3 class="mb-1">Presentation</h3>
                            <div class="px">
                                <p>Complaints: {{ $documentation->symptoms }}</p>
                                <p>History of Complaints: {{ $documentation->complaints_history }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 class="mb-1">Vitals</h3>

                        </div>
                        <div class="col-6">
                            <h3 class="mb-1">Initial Diagnosis</h3>
                            <div class="px">
                                <p>{{ $documentation->prognosis }}</p>

                            </div>
                        </div>
                        <div class="col-6 mt-1">
                            <h3 class="mb-1">Lab Investigations</h3>
                            <div class="px">
                                @forelse (($documentation->tests ?? []) as $test)
                                    <div class="mb-1">
                                        <p><b>{{ $test->name }}</b></p>
                                        @foreach (($test->results ?? []) as $r)
                                            <p>{{ $r->description }}: {{ $r->result }} {{ $r->unit }}
                                                {{ $r->reference_range ? "[$r->reference_range]" : null }}</p>
                                        @endforeach
                                    </div>
                                @empty
                                    No tests taken
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card py px mt-1">
            <div class="body">
                <div class="py">
                    @livewire('doctor.consultation-form', ['visit' => $documentation->visit])
                </div>
            </div>
        </div>
    </div>
@endsection
