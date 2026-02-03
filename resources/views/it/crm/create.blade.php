@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{asset('summernote/summernote-lite.min.css')}}">
@endpush

@section('content')
    <div class="container">
        <div class="card p-2">
            <form action="" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Cover Image</label>
                    <input type="file" name="image" accept=".jpg,.jpeg,.png" placeholder="Select a cover image" required class="form-control" />
                </div>
                <div class="form-group">
                    {{-- <label>Title</label> --}}
                    <input type="text" name="title" class="form-control my-1" id="title" required placeholder="Title" />
                </div>
                <textarea name="post" id="summernote-field">{{old('post')}}</textarea>

                <button class="btn btn-green my-1">Publish</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('summernote/summernote-lite.min.js')}}"></script>
    <script type="module">
        $(() => {
            $("#summernote-field").summernote({
                height: 300,
            });
        });
    </script>
@endpush
