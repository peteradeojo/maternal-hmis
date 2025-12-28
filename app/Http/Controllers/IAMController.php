<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Datalog;
use App\Models\User;
use Cloudinary\Api\HttpStatusCode;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class IAMController extends Controller
{
    public function index()
    {
        $rolesCount = Role::count();
        $permissionsCount = Permission::count();
        $auditLogsCount = AuditLog::count();
        $datalogsCount = Datalog::count();

        return view('iam.index', compact('rolesCount', 'permissionsCount', 'auditLogsCount', 'datalogsCount'));
    }

    public function roles()
    {
        return view('iam.roles');
    }

    public function permissions()
    {
        return view('iam.permissions');
    }

    public function auditLogs(Request $request)
    {
        return view('iam.audit-logs');
    }

    public function datalogs(Request $request)
    {
        return view('iam.datalogs');
    }

    public function getAuditLogs(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])->latest();

        return $this->dataTable($request, $query);
    }

    public function getDatalogs(Request $request)
    {
        $query = Datalog::with(['user'])->latest();

        return $this->dataTable($request, $query);
    }

    public function users(Request $request)
    {
        $users = User::all();
        return view('iam.users', compact('users'));
    }

    public function manageUser(Request $request, User $user)
    {
        $permissions = Permission::all();
        $roles = Role::all();
        return view('iam.user', compact('user', 'roles', 'permissions'));
    }

    public function saveUserRoles(Request $request, User $user)
    {
        $rUser = $request->user();

        if (!$rUser->hasRole('admin')) {
            return abort(403);
        }

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->input('roles')]);
        return response()->json([], HttpStatusCode::OK);
    }
}
