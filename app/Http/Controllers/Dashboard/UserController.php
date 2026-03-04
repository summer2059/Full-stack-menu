<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\User\UserRequest;
use App\Models\User;
use App\Services\Dashboard\User\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $title = 'Delete User!';
        $text  = 'Are you sure you want to delete?';
        confirmDelete($title, $text);

        if (request()->ajax()) {
            try {
                $users = $this->userService->getAllUsers();

                return datatables()->of($users)
                    ->addIndexColumn()
                    ->addColumn('role', fn($user) => $user->getRoleNames()->implode(', '))
                    ->addColumn('action', function ($user) {
                        $buttons = '';

                        if (auth()->user()->can('user.update')) {
                            $buttons .= '<a href="' . route('user.edit', $user->id) . '"
                                class="btn btn-sm btn-primary me-1">
                                <i class="fa fa-pencil me-1"></i>Edit
                            </a>';
                        }

                        if (auth()->user()->can('user.delete')) {
                            $buttons .= '
                                <form action="' . route('user.destroy', $user->id) . '" method="POST" style="display:inline;">
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
            return view('dashboard.users.form', compact('roles'));
        } catch (Exception $e) {
            Log::error('User create error: ' . $e->getMessage());
            toast('Failed to load create user form.', 'error');
            return redirect()->back();
        }
    }

    public function store(UserRequest $request)
    {
        try {
            $this->userService->createUser($request->validated());

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
            $roles           = Role::all();
            $userRole        = $user->roles->pluck('name')->first();
            $userPermissions = $user->permissions->pluck('name')->toArray();

            return view('dashboard.users.form', compact('user', 'roles', 'userRole', 'userPermissions'));
        } catch (Exception $e) {
            Log::error('User edit error: ' . $e->getMessage());
            toast('Failed to load edit form.', 'error');
            return redirect()->route('user.index');
        }
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            $this->userService->updateUser($user, $request->validated());

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
            $this->userService->deleteUser($user);
            toast('User deleted successfully.', 'success');
        } catch (Exception $e) {
            Log::error('User delete error: ' . $e->getMessage());
            toast('Failed to delete user.', 'error');
        }
        return redirect()->route('user.index');
    }

    public function getPermissionsByRole(Request $request)
    {
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json([]);
        }

        $permissions = $role->permissions->pluck('name');
        $grouped     = [];

        foreach ($permissions as $perm) {
            [$module, $action] = explode('.', $perm, 2);
            $module            = ucfirst(str_replace('_', ' ', $module));
            $grouped[$module][] = $perm;
        }

        return response()->json($grouped);
    }
}