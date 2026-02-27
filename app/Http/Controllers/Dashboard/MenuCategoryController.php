<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\CrudService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;

class MenuCategoryController extends Controller
{
    protected $crudService;
    protected $modelName;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
        $this->modelName   = 'MenuCategory';
    }

    public function index(Request $request)
    {
        $title = 'Delete Data!';
        $text  = 'Are you sure you want to delete?';
        confirmDelete($title, $text);

        if ($request->ajax()) {
            try {
                $data = $this->crudService->all($this->modelName);

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $buttons = '';

                        // EDIT — manager + admin
                        if (auth()->user()->can('menu_category.update')) {
                            $buttons .= '<a href="/dashboard/menu-category/' . $row->id . '/edit"
                                class="btn btn-sm btn-primary me-1">
                                <i class="fa fa-pencil me-1"></i>Edit
                            </a>';
                        }

                        // DELETE — admin only
                        if (auth()->user()->can('menu_category.delete')) {
                            $buttons .= '<a href="/dashboard/menu-category/' . $row->id . '"
                                class="btn btn-sm btn-danger"
                                data-confirm-delete="true">
                                <i class="fa fa-trash me-1"></i>Delete
                            </a>';
                        }

                        return $buttons ?: '<span class="text-muted small">View only</span>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('MenuCategory index error: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                return response()->json(['error' => 'Failed to fetch menu categories.'], 500);
            }
        }

        return view('dashboard.menu-category.index');
    }

    public function create()
    {
        return view('dashboard.menu-category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif',
            'priority' => 'required|integer',
            'price'    => 'required|integer',
            'rating'   => 'required|integer|min:0|max:5',
        ]);

        try {
            $data           = $request->all();
            $data['status'] = $request->has('status') ? 1 : 0;
            $this->crudService->create($this->modelName, $data);

            toast('Menu Category Added!', 'success');
            return redirect()->route('menu-category.index');
        } catch (\Exception $e) {
            Log::error('MenuCategory store error: ' . $e->getMessage(), [
                'data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            toast('Failed to add menu category.', 'error');
            return back()->withInput();
        }
    }

    public function edit(string $id)
    {
        try {
            $sc = $this->crudService->find($this->modelName, $id);
            return view('dashboard.menu-category.form', compact('sc'));
        } catch (\Exception $e) {
            Log::error('MenuCategory edit error: ' . $e->getMessage());
            toast('Failed to load menu category.', 'error');
            return redirect()->route('menu-category.index');
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'priority' => 'required|integer',
            'price'    => 'required|integer',
            'rating'   => 'required|integer|min:0|max:5',
        ]);

        try {
            $data           = $request->all();
            $data['status'] = $request->has('status') ? 1 : 0;
            $this->crudService->update($this->modelName, $id, $data);

            toast('Menu Category Updated!', 'success');
            return redirect()->route('menu-category.index');
        } catch (\Exception $e) {
            Log::error('MenuCategory update error: ' . $e->getMessage());
            toast('Failed to update menu category.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $this->crudService->delete($this->modelName, $id);
            toast('Menu Category Deleted!', 'success');
        } catch (\Exception $e) {
            Log::error('MenuCategory delete error: ' . $e->getMessage());
            toast('Failed to delete menu category.', 'error');
        }
        return redirect()->route('menu-category.index');
    }
}