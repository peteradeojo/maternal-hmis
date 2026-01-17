@extends('layouts.app')
@section('title', $user->name)

@section('content')
    <div class="container grid gap-y-4">
        <div class="card p-4" x-data="{
            roles: [],
            updateRoles(role) {
                if (this.roles.includes(role)) {
                    this.roles = this.roles.filter((r) => r != role);
                } else {
                    this.roles.push(role);
                }
            },
            async saveRoles() {
                const roles = [...this.roles];
                try {
                    await axios.post(`{{ route('iam.save-user-roles', $user) }}`, { roles });
                    location.reload();
                } catch (error) {
                    console.error(error);
                    notifyError(`Unable to save role`);
                }
            }
        }" x-init="roles = {{ $user->roles->pluck('name') }}">
            <p class="basic-header">{{ $user->name }}</p>
            <p>{{ $user->department->name }}</p>
            <p>Roles: {{ $user->roles->pluck('name')->join(', ') }}</p>

            <p class="basic-header">Roles</p>
            <div class="grid md:grid-cols-3 gap-4">
                @foreach ($roles as $i => $role)
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="roles[{{ $role->name }}]" @checked($user->roles->pluck('name')->contains($role->name))
                                @change="updateRoles('{{ $role->name }}')" />
                            {{ ucfirst($role->name) }}
                        </label>
                    </div>
                @endforeach
            </div>
            <button @click="saveRoles" class="btn bg-blue-500 text-white">Save <i class="fa fa-save"></i></button>
        </div>

        <div class="card py px">
            <div class="header">
                <div class="card-header">{{ $user->name }}</div>
            </div>
            <div class="body py-1">
                <div class="row">
                    <div class="col-6">
                        <p><strong>Department:</strong> {{ $user->department->name }}</p>
                        <p><strong>Phone:</strong> {{ $user->phone }}</p>
                    </div>
                    <div class="col-6">
                        <p><strong>Created:</strong> {{ $user->created_at->format('Y-m-d') }}</p>
                        <p><strong>Updated:</strong> {{ $user->updated_at->format('Y-m-d') }}</p>
                    </div>
                </div>

                <form action="{{ route('it.staff.view', $user) }}" method="post" class="mt-2">
                    @csrf
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id" class="form-control">
                            @foreach ($departments as $option)
                                <option value="{{ $option->id }}" @match($option->id, $user->department_id) selected="true"
                                    @endmatch>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Change Password" />
                    </div>
                    <button type="submit" class="btn btn-blue">Submit</button>
                </form>

                <form action="{{route('it.staff.update-status', $user)}}" method="post">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label>Enable/Disable this account</label>
                        <div class="flex-center gap-x-4">
                            <select name="account_status" class="form-control" required="required">
                                <option @selected($user->status == Status::active) value="enable">Enable</option>
                                <option @selected($user->status == Status::blocked) value="disable">Disabled</option>
                            </select>
                            <button class="btn bg-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
