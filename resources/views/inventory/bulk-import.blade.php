@extends('layouts.app')

@section('content')
    <div class="card">
        <p class="basic-header">Bulk Import</p>

        <form action="" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label>Select file</label>
                <input type="file" name="import" class="form-control" required />
            </div>
            <div class="form-group">
                <button class="btn bg-blue-400 text-white">Submit <i class="fa fa-save"></i></button>
            </div>
        </form>
    </div>
@endsection
