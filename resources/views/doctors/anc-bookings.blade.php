@extends('layouts.app')

@section('content')
    <div class="card py px">
        <div class="header">
            <p class="card-header">New Antenatal Bookings</p>
        </div>
        <div class="body py-2">
            @include('components.pendingAncBookings', ['url' => '/med/anc-bookings/'])
        </div>
    </div>
@endsection
