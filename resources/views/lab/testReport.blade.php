@extends('layouts.app')
@section('title', 'Test Report :: ' . $doc->patient->name)

@section('content')
    <div class="card py px">
        <div class="header card-header">{{ $doc->patient->name }}</div>
        <div class="body py">
            <div class="row">
                <div class="col-6">
                    <p><b>Name: </b> {{ $doc->patient->name }}</p>
                    <p><b>Category: </b> {{ $doc->patient->category->name }}</p>
                    <p><b>Card Number: </b> {{ $doc->patient->card_number }}</p>
                    <p><b>Date: </b> {{ $doc->created_at->format('Y-m-d h:i A') }}</p>
                    <p><b>Date Completed: </b> {{ $doc->tests->last()->updated_at->format('Y-m-d h:i A') }}</p>
                </div>
                <div class="col-6"></div>
            </div>

            <div class="mt-2">
                <div class="card-header">Report</div>
                @include('lab.components.test-results', ['tests' => $doc->tests])
            </div>
            <div class="my">
                <p><b>Comments</b></p>
                <p>No Comment</p>
            </div>
        </div>
    </div>
    </div>
@endsection
