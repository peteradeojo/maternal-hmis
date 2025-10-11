@extends('layouts.app')

@section('content')
    <div class="card py px foldable">
        <div class="foldable-header header">
            <div class="card-header">Wards</div>
        </div>
        <div class="foldable-body unfolded">
            <div class="pt-1"></div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Beds</th>
                        <th>Available Spaces</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $w)
                        <tr>
                            <td>{{ $w->name }}</td>
                            <td>{{ $w->type }}</td>
                            <td>{{ $w->beds }}</td>
                            <td>{{ $w->beds - $w->filled_beds }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="pt-1"></div>
    <div class="card py px">
        <form action="" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" required />
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select name="type" id="type" class="form-control">
                    <option>Select a Type</option>
                    <option value="private">Private</option>
                    <option value="public">Public</option>
                </select>
            </div>
            <div class="form-group">
                <label for="beds">Beds</label>
                <input type="text" name="beds" id="beds" class="form-control" required value="1" />
            </div>
            <div class="form-group">
                <button class="btn btn-blue">Submit</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            $("table").DataTable({
                responsive: true,
            });
        })
    </script>
@endpush
