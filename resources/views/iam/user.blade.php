@extends('layouts.app')
@section('title', $user->name)

@section('content')
    <div class="container card p-4" x-data="{
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
        <div class="grid grid-cols-3 gap-4">
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
@endsection
