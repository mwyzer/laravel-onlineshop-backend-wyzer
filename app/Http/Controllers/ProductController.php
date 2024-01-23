<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::with('category'); // Eager load the category relationship

        // Check if a search term is provided in the request
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        $products = $query->paginate(10);

        // Pass the search term to the view for displaying in the search input
        $searchTerm = $request->input('search');

        return view('pages.product.index', compact('products', 'searchTerm'));
    }
    // {
    //     $products = DB::table('users')
    //     ->when($request->input('search'), function ($query, $search) {
    //         return $query->where(function ($query) use ($search) {
    //             $query->where('name', 'like', '%' . $search . '%');
    //         });
    //     })
    //     ->paginate(10);

    //     // $products = \App\Models\Product::paginate(10);

    //     return view('pages.product.index', compact('products'));
    // }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('pages.product.create', compact('categories'));
    }

    // store
    public function store(Request $request)
    {
        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);
        // $data = $request->all();

        $product = new \App\Models\Product;
        $product -> name = $request -> name;
        $product -> price = (int) $request -> price;
        $product -> stock = (int) $request -> stock;
        $product -> category_id = $request -> category_id;
        $product -> image = $filename;
        $product -> save();

        return redirect()->route('product.index')->with('success', 'Product added successfully');
    }

    //edit
    public function edit($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $categories = \App\Models\Category::all();
        $filename = $product->image;

        return view('pages.product.edit', compact('product', 'categories', 'filename'));
    }

    //update
    public function update(Request $request, $id) {
        $product = \App\Models\Product::findOrFail($id);

        // Validate other fields if needed
        $validated = $request->validate([
            'name' => 'required|max:255',
            // Add validation rules for other fields
        ]);

        // Update the 'image' attribute only if a new image is provided
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);

            ddd($request->image);

            $product->image->$filename;

            // Delete the old image if it exists
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }

            $validated['image'] = $filename;
        }

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        try {
            $product = \App\Models\Product::findOrFail($id);
            $product->delete();

            return redirect()->route('product.index')->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('product.index')->with('error', 'Failed to delete the product');
        }
    }
};
