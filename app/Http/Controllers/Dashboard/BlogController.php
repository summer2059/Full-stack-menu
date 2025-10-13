<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlogImage;
use App\Services\CrudService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    protected  $crudService;
    protected $modelName;

    public function __construct(CrudService $crudService)
    {
        $this->crudService = $crudService;
        $this->modelName = 'Blog';
        $this->middleware('auth'); 
    }

    public function index(Request $request){
        try {
            $title = 'Delete Data!';
            $text = 'Are you sure you want to delete?';
            confirmDelete($title, $text);
            
            if ($request->ajax()) {
                $data = $this->crudService->all($this->modelName);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($data) {
                        $actionBtn = '<a href="/dashboard/blog/' . $data->id . '" class="btn btn-sm btn-info"> Show</a>
                            <a href="/dashboard/blog-image/' . $data->id . '" class="btn btn-sm btn-success"> Images</a>
                            <a href="/dashboard/blog/' . $data->id . '/edit" class="btn btn-sm btn-primary"> Edit</a>
                            <a href="/dashboard/blog/' . $data->id . '" class="btn btn-sm btn-danger" data-confirm-delete="true"> Delete</a>';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('dashboard.blog.index');
        } catch (\Exception $e) {
            Log::error('Error fetching blog index: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('An error occurred while fetching the blog data.', 'error');
            return redirect()->route('blog.index');
        }
    }

    public function create(){
        try{
            return view('dashboard.blog.create');
        } catch(\Exception $e){
            Log::error('Error fetching blog create: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        
    }

    public function store(Request $request){
        try {
            $request->validate([
                'title' => 'required',
                'images.*' => 'image|mimes:jpg,jpeg,png,gif',
            ]);

            $data = $request->only(['title', 'slug', 'description', 'status']);
            $data['username'] = auth()->user()->name; 

            $blog = $this->crudService->create($this->modelName, $data);

            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $image) {
            //         $blog->images()->create(['images' => $this->crudService->uploadImage($image)]);
            //     }
            // }
            if($request->hasFile('images')){
                foreach($request->file('images') as $image){
                    $blog->images()->create(['images' => $this->crudService->uploadFile($image, 'uploads/images'),]);
                }
            }

            toast('Blog created successfully', 'success');
            return redirect()->route('blog.index');

        } catch (\Exception $e) {
            Log::error('Error creating blog: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('Error creating blog: ' , 'error');
            return back()->withInput();
        }
    }

    public function edit(string $id){
        try {
            $data = $this->crudService->find($this->modelName, $id);
            $images = BlogImage::where('blog_id', $id)->get();
            return view('dashboard.blog.edit', compact('data', 'images'));
        } catch (\Exception $e) {
            Log::error('Error fetching blog edit: ' . $id . ':' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('Error occured while opening the edit data: ', 'error');
            return redirect()->route('blog.index');
        }
    }

    public function update(Request $request, string $id){
        try {
            $this->validate($request, [
                'title' => 'required',
            ]);

            $data = $request->only(['title', 'slug', 'description', 'status']);
            $data['username'] = auth()->user()->name; 

            $this->crudService->update($this->modelName, $id, $data);

            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $image) {
            //         $destinationPath = 'uploads/images/';
            //         $imageName = date('YmdHis') . '.' . $image->getClientOriginalExtension();
            //         $image->move($destinationPath, $imageName);
            //         $imagePath = $destinationPath . $imageName;

            //         BlogImage::create([
            //             'blog_id' => $id,
            //             'images' => $imagePath,
            //         ]);
            //     }
            // }
            if($request->hasFile('images')){
                foreach($request->file('images') as $image){
                    $uploadedImage = $this->crudService->uploadFile($image, 'uploads/imaes');
                    BlogImage::create(['blog_id' => $id, 'images'=>$uploadedImage]);
                }
            }

            toast('Blog updated successfully', 'success');
            return redirect()->route('blog.index');

        } catch (\Exception $e) {
            Log::error('Error updating blog: ' . $id . ':' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('Error occurred while updating the blog: ', 'error');
            return back()->withInput();
        }
    }

    public function destroy(string $id){
        try {
            BlogImage::where('blog_id', $id)->delete();
            $this->crudService->delete($this->modelName, $id);
            toast('Blog deleted successfully', 'success');
            return redirect()->route('blog.index');
        } catch (\Exception $e) {
            Log::error('Error deleting blog: ' . $id . ':' . $e->getMessage(),[
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('Error Occured while deleting the blog: ', 'error');
            return redirect()->route('blog.index');
        }
    }

    public function getBlogImage($id){
        try {
            $image = BlogImage::where('blog_id', $id)->get();
            return view('dashboard.blog.image', compact('image', 'id'));
        } catch (\Exception $e) {
            Log::error('Error getting blog image: ' . $id . ':' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            toast('Error occured while fetching the images: ' , 'error');
            return redirect()->route('blog.index');
        }
    }

    public function uploadImages(Request $request)
    {
        try {
            $request->validate([
                'images.*' => 'image|mimes:jpeg,png,jpg,gif',
            ]);

            $uploadedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/images'), $imageName);

                    // Store in BlogImage
                    BlogImage::create([
                        'blog_id' => $request->product_id,
                        'images' => $imageName,
                    ]);

                    $uploadedImages[] = $imageName; // Store the image name for response
                }
            }

            return response()->json(['success' => true, 'images' => $uploadedImages]);
        } catch (\Exception $e) {
            Log::error('Error uploading images: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error uploading images: ' ]);
        }
    }

    public function deleteImage(Request $request, $imageId)
    {
        try {
            $image = BlogImage::findOrFail($imageId);
            $this->crudService->deleteFileIfExists('uploads/images'. $image->images);
            $image->delete();

            return response()->json(['success' => false, 'message' => 'Image not found.']);
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Error deleting image: ' ]);
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $blog = $this->crudService->find($this->modelName, $id);

            if ($blog) {
                $blog->status = !$blog->status; // Toggle status
                $blog->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Status updated successfully',
                    'status' => $blog->status,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling status: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error toggling status: ' ,
            ]);
        }
    }
}
