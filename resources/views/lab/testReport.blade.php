@extends('layouts.app')
@section('title', 'Test Report :: ' . $patient->name)

@section('content')
    <div class="card py px">
        <div class="header card-header">{{ $patient->name }}</div>
        <div class="body py">
            <div class="row">
                <div class="col-6">
                    <p><b>Name: </b> {{ $patient->name }}</p>
                    <p><b>Category: </b> {{ $patient->category->name }}</p>
                    <p><b>Card Number: </b> {{ $patient->card_number }}</p>
                    {{-- <p><b>Date: </b> {{ $created_at->format('Y-m-d h:i A') }}</p>
                    <p><b>Date Completed: </b> {{ $doc->tests->last()->updated_at->format('Y-m-d h:i A') }}</p> --}}
                </div>
                <div class="col-6"></div>
            </div>

            <div class="mt-2">
                <div class="card-header py-3">Report</div>
                @include('lab.components.test-results', ['tests' => $tests])
            </div>
            {{-- <div class="my">
                <p><b>Comments</b></p>
                <p>No Comment</p>
            </div> --}}
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            function printElement(e) {
                let cloned = e.cloneNode(true);
                document.body.appendChild(cloned);
                cloned.classList.add("printable");

                console.log(cloned);
                window.print();
                document.body.removeChild(cloned);
            }
        })
    </script>
@endpush
