<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Services\CrudService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    protected $crudService;
    protected $modelName;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
        $this->modelName = 'Menu';
    }

    public function index(Request $request)
    {
        $title = 'Delete Data!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        if ($request->ajax()) {
            $data = $this->crudService->all($this->modelName);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category_title', function ($data) {
                    return $data->menuCategory ? $data->menuCategory->title : 'N/A';
                })
                ->addColumn('status', function ($data) {
                    return $data->status ? 1 : 0;
                })
                ->addColumn('action', function ($data) {
                    return '
                        <a href="' . route('menu.edit', $data->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <a href="' . route('menu.destroy', $data->id) . '" 
                            class="btn btn-sm btn-danger" 
                            data-confirm-delete="true">Delete</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.menu.index');
    }

    public function create()
    {
        $categories = MenuCategory::orderBy('title', 'asc')->get();
        return view('dashboard.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'image' => 'required',
            'menu_category_id' => 'required|exists:menu_categories,id',
        ]);

        try {
            $data = $request->all();
            $data['status'] = $request->has('status') ? 1 : 0;

            $this->crudService->create($this->modelName, $data);

            toast('Menu Added Successfully!', 'success');
            return redirect()->route('menu.index');

        } catch (Exception $e) {
            Log::error('Menu Store Error: '.$e->getMessage());
            toast('Failed to add menu. Please try again.', 'error');
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->crudService->find($this->modelName, $id);
            $categories = MenuCategory::orderBy('title', 'asc')->get();
            return view('dashboard.menu.edit', compact('data', 'categories'));
        } catch (Exception $e) {
            Log::error('Menu Edit Error: '.$e->getMessage());
            toast('Menu not found!', 'error');
            return redirect()->route('menu.index');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'image' => 'nullable',
            'menu_category_id' => 'required|exists:menu_categories,id',
        ]);

        try {
            $data = $request->all();
            $data['status'] = $request->has('status') ? 1 : 0;

            $this->crudService->update($this->modelName, $id, $data);

            toast('Menu Updated Successfully!', 'success');
            return redirect()->route('menu.index');

        } catch (Exception $e) {
            Log::error('Menu Update Error: '.$e->getMessage());
            toast('Failed to update menu. Please try again.', 'error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->crudService->delete($this->modelName, $id);
            toast('Menu Deleted Successfully!', 'success');
        } catch (Exception $e) {
            Log::error('Menu Delete Error: '.$e->getMessage());
            toast('Failed to delete menu. Please try again.', 'error');
        }

        return redirect()->route('menu.index');
    }
}
