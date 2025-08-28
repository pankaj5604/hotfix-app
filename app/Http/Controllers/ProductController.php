<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // ✅ Bulk Add Products
    public function store(Request $request)
    {
        $request->validate([
            'type'   => 'required|array',
            'type.*' => 'required|string|max:255',
        ]);

        $products = [];

        foreach ($request->type as $type) {
            $products[] = Product::create([
                'type' => $type,
            ]);
        }

        return response()->json([
            'message'  => 'Products created successfully!',
            'products' => $products,
        ], 201);
    }

    // ✅ List Products
    public function index()
    {
        return response()->json(Product::all());
    }
}