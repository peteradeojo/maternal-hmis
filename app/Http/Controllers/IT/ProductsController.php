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
                $query->where('name', 'ilike', "$search%")->orWhereHas('category', function ($q) use ($search) {
                    $q->where('name', 'ilike', "%$search%");
                });
            }
        ]);
    }

    public function addProducts(Request $request)
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'category' => 'required|string',
            'products' => 'file|mimes:xlsx,csv'
        ]);

        $category = strtoupper($request->category);
        $c = ProductCategory::where('name', $category)->where('department_id', $request->department_id)->first();
        if (!$c) {
            ProductCategory::create([
                'name' => $category,
                'department_id' => $request->department_id,
            ]);
        }

        $file = $request->file('products');
        $path = $file?->store('products');

        if ($path) {
            Artisan::queue('app:load-products', [
                'category' => $category,
                'path' => storage_path("app/$path"),
                'type' => $file->getClientOriginalExtension(),
                '--delete' => true,
            ]);
            // return back()->with('success', true);
            session()->flash('success', true);
        }

        Product::create([
            'name' => $request->name,
            'amount'  => $request->amount,
            'product_category_id' => $c->id,
        ]);

        return back();
    }

    public function show(Request $request, Product $product)
    {
        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'product_category_id' => 'required|exists:product_categories,id',
                'amount' => 'required|numeric',
                'is_visible' => 'nullable'
            ]);

            $data['is_visible'] = @$data['is_visible'] == 'on' ? 1 : 0;

            $product->update($data);
            return redirect()->route('it.products');
        }

        $categories = ProductCategory::all();
        return view('it.products.show', compact('product', 'categories'));
    }
}
