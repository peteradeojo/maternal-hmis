@extends('layouts.app')
@section('title', 'Dropbox')

@section('content')
    <div class="container grid gap-4">
        <div class="card p-4">
            <div class="card-header">Dropbox</div>

            {{-- <table id="table" class="table">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table> --}}

            <x-datatables id="table">
                <x-slot:thead>
                    <tr>
                        <th>Title</th>
                        <th>From</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </x-slot:thead>
            </x-datatables>
        </div>

        <div class="card p-4">
            <div class="card-header">Upload</div>

            <form action="" method="post" enctype="multipart/form-data" x-data="{ to: 'dept' }">
                @csrf
                <div class="form-group">
                    <label>File</label>
                    <input type="file" name="file" accept=".jpg,.png,.docx,.xlsx,.xls,.doc,.odt,.pdf"
                        class="form-control" required />
                </div>

                <div class="form-group">
                    <label class="block">Receiver (s)</label>

                    <div class="flex-center py-2 gap-x-4">
                        <label>
                            <input type="radio" name="to" :checked="to == 'dept'" value="dept"
                                x-on:change="$event.target.checked && (to = 'dept')" />
                            Department
                        </label>
                        <label>
                            <input :checked="to == 'user'" type="radio" name="to" value="user"
                                x-on:change="$event.target.checked && (to = 'user')" />
                            User
                        </label>

                    </div>
                </div>

                <template x-if="to == 'user'">
                    <div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                placeholder="Enter user's phone number" required />
                        </div>
                    </div>
                </template>
                <template x-if="to == 'dept'">
                    <div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="department" class="form-control">
                                <option value="all" selected>ALL</option>
                                @foreach ($departments as $case)
                                    <option value="{{ $case['id'] }}">{{ $case['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </template>

                <div class="form-group">
                    <button class="btn bg-primary text-white">Upload <i class="fa fa-upload"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(() => {
            const table = $("#table").DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('dropbox') }}?fetch",
                },
                columns: [
                    { data: 'file_name', },
                    { data: 'sender.name', },
                    { data: ({created_at}) => parseDateFromSource(created_at) },
                    {
                        data: (row) => `<a href='{{route('dropbox.download', ':id')}}' class='btn bg-primary'>Download</a>`.replace(':id', row.id),
                    },
                ],
            });
        });
    </script>
@endpush
