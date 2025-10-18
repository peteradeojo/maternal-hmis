@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-1">
            <div class="header flex justify-between items-center">
                <p class="text-xl font-semibold">Blog Posts</p>
                <a href="{{ route('it.crm-publish') }}" class="link">Create Post</a>
            </div>
            <div class="body">
                <table class="table" id="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            @php
                                $post = (object) $post;
                            @endphp
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->created_at }}</td>
                                <td></td>
                                <td>
                                    <a href="{{ route('it.crm-show', $post->id) }}" class="link">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("#table").DataTable();
        });
    </script>
@endpush
