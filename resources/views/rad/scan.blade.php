@extends('layouts.app')

@section('content')
    <div class="card py px">
        <p class="card-header">{{ $doc->patient->name }}</p>
        <hr>
        <div class="body py-2">
            <p><b>Requested by: </b> {{ $doc->requester->name }} </p>
            <p><b>Time: </b> {{ $doc->created_at->format('Y-m-d h:i A') }}</p>
            <p><b>Investigation: </b> <span class="underline">{{ $doc->name }}</span></p>

            <div class="py-3"></div>

            <form action="" method="post" enctype="multipart/form-data">
                @csrf

                @if ($doc->secure_path)
                    @if (str_ends_with($doc->secure_path, 'pdf'))
                        <iframe src="{{ $doc->secure_path }}" frameborder="0" width="100%" height="400"
                            allowfullscreen="true"></iframe>
                    @else
                        <img src="{{ $doc->secure_path }}" class="w-3/5 m-auto p-1" alt="">
                    @endif
                @else
                    <p>No result</p>
                @endif

                <div class="form-group">
                    <label for="result_file" data-action="drag_drop" class="drag_drop" id="result_label">
                        <p>&plus; Add a file</p>
                        <p>Or drag into this box</p>
                    </label>
                </div>

                <input type="file" id="result_file" name="result_file" class="hidden" />

                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea name="comment" id="comment" cols="30" placeholder="Add a comment" class="form-control"></textarea>
                </div>

                <button class="btn btn-blue">Submit Result</button>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(() => {
            document.querySelectorAll("[data-action='drag_drop']").forEach((elem, i) => {
                elem.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    elem.classList.add("bg-gray-100");
                });
                elem.addEventListener("dragenter", (e) => {
                    e.preventDefault();
                    elem.classList.add("bg-gray-100");
                });
                elem.addEventListener("dragleave", (e) => {
                    e.preventDefault();

                    elem.classList.remove("bg-gray-100");
                });

                elem.addEventListener('dragend', (e) => {
                    e.preventDefault();

                    elem.classList.remove("bg-gray-100");
                });

                elem.addEventListener("drop", (ev) => {
                    ev.preventDefault();

                    // Use DataTransfer interface to access the file(s)
                    [...ev.dataTransfer.files].forEach((file, i) => {
                        document.getElementById("result_file").files[0] = file;
                        document.getElementById("result_label").innerHTML =
                            `<p>${file.name}</p>`;
                    });
                });
            })

            document.querySelector("#result_file").addEventListener("change", (e) => {
                const file = e.target.files[0];
                document.getElementById("result_label").innerHTML = `<p>${file.name}</p>`;
            });
        });
    </script>
@endpush
