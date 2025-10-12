@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-2">
            <form action="" method="post">
                @csrf
                <div class="form-group">
                    {{-- <label>Title</label> --}}
                    <input type="text" name="title" class="form-control my-1" id="title" required placeholder="Title" />
                </div>
                <textarea name="post" id="summernote">{{old('post')}}</textarea>

                <button class="btn btn-green my-1">Publish</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
    <script>
        $(() => {
            $("#summernote").summernote({
                height: 300,
            });
        });
    </script>
@endpush
