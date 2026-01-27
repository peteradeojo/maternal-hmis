@extends('layouts.app')

@section('title', 'IAM Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6 grid gap-y-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Identity & Access Management</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                        <i class="fa fa-user-shield text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Roles</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $rolesCount }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('iam.roles') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Manage
                        Roles &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                        <i class="fa fa-key text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Permissions</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $permissionsCount }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('iam.permissions') }}"
                        class="text-green-500 hover:text-green-700 text-sm font-medium">View Permissions &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                        <i class="fa fa-history text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Audit Logs</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $auditLogsCount }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('iam.audit-logs') }}"
                        class="text-purple-500 hover:text-purple-700 text-sm font-medium">View Audit Trail &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                        <i class="fa fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Access Failures</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $datalogsCount }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('iam.datalogs') }}" class="text-red-500 hover:text-red-700 text-sm font-medium">View
                        Failures &rarr;</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                        <i class="fa fa-person text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase font-semibold">Users</p>
                        {{-- <p class="text-2xl font-bold text-gray-800">{{ $datalogsCount }}</p> --}}
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('iam.users') }}" class="text-red-500 hover:text-red-700 text-sm font-medium">Manage Users &rarr;</a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">Recent Security Events</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 italic">Select a category above to view detailed logs and manage access controls.
                </p>
            </div>
        </div>
    </div>
@endsection
