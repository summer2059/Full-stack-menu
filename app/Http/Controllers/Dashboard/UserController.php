<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Services\CrudService;
use Illuminate\Support\Facades\Log;
use Exception;

class UserController extends Controller
{
    protected $crudService;
    protected $modelName;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
        $this->modelName = 'user';
    }

    public function index()
    {
        $title = 'Delete User!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        if (request()->ajax()) {
            try {
                $data = $this->crudService->all($this->modelName);

                return datatables()->of($data)
                    ->addIndexColumn()
                    ->addColumn('role', function ($user) {
                        return $user->getRoleNames()->implode(', ');
                    })
                    ->addColumn('action', function ($data) {
                        return '
                            <a href="' . route('user.edit', $data->id) . '" class="btn btn-sm btn-primary">Edit</a>
                            <form action="' . route('user.destroy', $data->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete="true">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (Exception $e) {
                Log::error('User index AJAX error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to load users.'], 500);
            }
        }

        return view('dashboard.users.index');
    }

    public function create()
    {
        try {
            $roles = Role::all();
            return view('dashboard.users.create', compact('roles'));
        } catch (Exception $e) {
            Log::error('User create error: ' . $e->getMessage());
            toast('Failed to load create user form.', 'error');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'required|string|email|max:255|unique:users',
                'password'    => 'required|string|min:6|confirmed',
                'role'        => 'required|exists:roles,name',
                'permissions' => 'array'
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);

            $user->assignRole($validated['role']);

            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            toast('User created successfully.', 'success');
            return redirect()->route('user.index');
        } catch (Exception $e) {
            Log::error('User store error: ' . $e->getMessage());
            toast('Failed to create user.', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function edit(User $user)
    {
        try {
            $roles = Role::all();
            $userRole = $user->roles->pluck('name')->first();
            $userPermissions = $user->permissions->pluck('name')->toArray();

            return view('dashboard.users.edit', compact('user', 'roles', 'userRole', 'userPermissions'));
        } catch (Exception $e) {
            Log::error('User edit error: ' . $e->getMessage());
            toast('Failed to load edit form.', 'error');
            return redirect()->route('user.index');
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password'    => 'nullable|string|min:6|confirmed',
                'role'        => 'required|exists:roles,name',
                'permissions' => 'array'
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            $user->save();

            $user->syncRoles([$validated['role']]);

            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            } else {
                $user->syncPermissions([]);
            }

            toast('User updated successfully.', 'success');
            return redirect()->route('user.index');
        } catch (Exception $e) {
            Log::error('User update error: ' . $e->getMessage());
            toast('Failed to update user.', 'error');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            toast('User deleted successfully.', 'success');
        } catch (Exception $e) {
            Log::error('User delete error: ' . $e->getMessage());
            toast('Failed to delete user.', 'error');
        }

        return redirect()->route('user.index');
    }


    public function getPermissionsByRole(Request $request)
{
    $role = \Spatie\Permission\Models\Role::where('name', $request->role)->first();

    if (!$role) {
        return response()->json([]);
    }

    $permissions = $role->permissions->pluck('name');
    $grouped = [];

    foreach ($permissions as $perm) {
        // Split by module name (before the dot)
        [$module, $action] = explode('.', $perm, 2);
        $module = ucfirst(str_replace('_', ' ', $module));
        $grouped[$module][] = $perm;
    }

    return response()->json($grouped);
}

}
