@extends('layouts.app')

@section('content')
    @livewire('nursing.anc-bookings')

    <div class="card py px">
        <div class="header">
            <h1>New Antenatal Bookings</h1>
        </div>
        <div class="my-2">
            @include('components.pendingAncBookings')
        </div>
    </div>
@endsection
