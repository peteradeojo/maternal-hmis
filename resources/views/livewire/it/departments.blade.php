<div>
    <div class="card py px">
        <div class="header card-header">Departments</div>
        <div class="body py">
            <table id="departments-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr>
                            <td>{{ $d->name }}</td>
                            <td>{{ $d->description }}</td>
                            <td><a href="{{ route('it.department', $d) }}">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(() => {
            $("#departments-table").DataTable();
        });
    </script>
@endpush
