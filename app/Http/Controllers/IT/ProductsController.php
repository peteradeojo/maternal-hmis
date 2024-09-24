<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ProductsController extends Controller
{
    public function index()
    {
        $cats = ProductCategory::all();
        $departments = Department::all();
        return view('it.products', ['categories' => $cats, 'departments' => $departments]);
    }

    public function fetchProducts(Request $request)
    {
        return $this->dataTable($request, Product::with(['category']), [
            function ($query, $search) {
                $query->where('name', 'like', "$search%")->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            }
        ]);
    }

    public function addProducts(Request $request)
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'category' => 'required|string',
            'products' => 'required|file|mimes:xlsx,csv'
        ]);

        $file = $request->file('products');
        $path = $file->store('products');

        if ($path) {
            $category = strtoupper($request->category);
            $c = ProductCategory::where('name', $category)->where('department_id', $request->department_id)->first();
            if (!$c) {
                ProductCategory::create([
                    'name' => $category,
                    'department_id' => $request->department_id,
                ]);
            }

            Artisan::queue('app:load-products', [
                'category' => $category,
                'path' => storage_path("app/$path"),
                'type' => $file->getClientOriginalExtension(),
                '--delete' => true,
            ]);

            return back()->with('success', true);
        }

        return back()->withErrors("Unable to upload file");
    }
}
