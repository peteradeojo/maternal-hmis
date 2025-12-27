@extends('layouts.app')

@section('title', 'Manage Permissions')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Permission Management</h1>
            <a href="{{ route('iam.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-2"></i> Back to IAM
            </a>
        </div>

        @livewire('iam.permission-manager')
    </div>
@endsection