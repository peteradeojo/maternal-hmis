@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-1">
            <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
            <p><small>Posted: <i>{{ $post->created_at?->format('Y-m-d H:i A') }}</i> by
                    <u>{{ $post->user }}</u></small></p>
            <div>{!! $data !!}</div>
        </div>
    </div>
@endsection
