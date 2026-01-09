@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-1">
            <h1 class="text-3xl font-bold">{{ $post->title }}</h1>
            <p><small>Posted: <i>{{ $post->created_at?->format('Y-m-d H:i A') }}</i> by
                    <u>{{ $post->user }}</u></small></p>
            <div>{!! $data !!}</div>
        </div>

        <form action="{{route('it.crm-status', $post)}}" method="post">
            @csrf
            @method('PUT')

            <div class="form-group">
                <select name="status" class="form-control">
                    <option @selected($post->status == 1) value="1">Active</option>
                    <option @selected($post->status == 0) value="0">Disable</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn bg-primary text-white">Submit</button>
            </div>
        </form>
    </div>
@endsection
