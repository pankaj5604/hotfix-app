<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{

    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'          => 'required|string|max:255',
            'product_rate'  => 'required|numeric|min:0',
            'employee_rate' => 'required|numeric|min:0',
        ]);

        $product = Product::create([
            'type'          => $request->type,
            'product_rate'  => $request->product_rate,
            'employee_rate' => $request->employee_rate,
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'error' => 'Product not found',
            ], 404);
        }

        $request->validate([
            'type'          => 'required|string|max:255',
            'product_rate'  => 'required|numeric|min:0',
            'employee_rate' => 'required|numeric|min:0',
        ]);

        $product->update([
            'type'          => $request->type,
            'product_rate'  => $request->product_rate,
            'employee_rate' => $request->employee_rate,
        ]);

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ], 201);
    }
}