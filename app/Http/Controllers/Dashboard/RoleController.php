<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\User\RoleRequest;
use App\Services\Dashboard\User\RoleService;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $title = 'Manage Roles';
        $text  = 'Are you sure you want to delete this role?';
        confirmDelete($title, $text);

        if (request()->ajax()) {
            try {
                $roles = $this->roleService->getAllRoles();

                return datatables()->of($roles)
                    ->addIndexColumn()
                    ->addColumn('action', function ($role) {
                        $buttons = '';

                        // EDIT
                        if (auth()->user()->can('role.update')) {
                            $buttons .= '<a href="' . route('role.edit', $role->id) . '" class="btn btn-sm btn-primary me-1">
                                <i class="fa fa-pencil me-1"></i>Edit
                            </a>';
                        }

                        // DELETE
                        if (auth()->user()->can('role.delete')) {
                            $buttons .= '
                                <form action="' . route('role.destroy', $role->id) . '" method="POST" style="display:inline;">
                                    ' . csrf_field() . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete="true">
                                        <i class="fa fa-trash me-1"></i>Delete
                                    </button>
                                </form>';
                        }

                        return $buttons ?: '<span class="text-muted small">—</span>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (Exception $e) {
                Log::error('Role index AJAX error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to load roles.'], 500);
            }
        }

        return view('dashboard.roles.index');
    }

    public function create()
    {
        try {
            return view('dashboard.roles.form');
        } catch (Exception $e) {
            Log::error('Role create error: ' . $e->getMessage());
            toast('Failed to load create role form.', 'error');
            return redirect()->back();
        }
    }

    public function store(RoleRequest $request)
    {
        try {
            $role = $this->roleService->createRole($request->validated());

            toast('Role created successfully.', 'success');
            return redirect()->route('role.index');
        } catch (Exception $e) {
            Log::error('Role store error: ' . $e->getMessage());
            toast('Failed to create role.', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            return view('dashboard.roles.form', compact('role'));
        } catch (Exception $e) {
            Log::error('Role edit error: ' . $e->getMessage());
            toast('Failed to load edit form.', 'error');
            return redirect()->route('role.index');
        }
    }

    public function update(RoleRequest $request, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $this->roleService->updateRole($role, $request->validated());

            toast('Role updated successfully.', 'success');
            return redirect()->route('role.index');
        } catch (Exception $e) {
            Log::error('Role update error: ' . $e->getMessage());
            toast('Failed to update role.', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy($roleId)
    {
        try {
            $role = Role::findOrFail($roleId);
            $this->roleService->deleteRole($role);

            toast('Role deleted successfully.', 'success');
        } catch (Exception $e) {
            Log::error('Role delete error: ' . $e->getMessage());
            toast('Failed to delete role.', 'error');
        }
        return redirect()->route('role.index');
    }
}