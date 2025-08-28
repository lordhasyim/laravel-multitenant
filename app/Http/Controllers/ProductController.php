<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            'products' => $products,
            'tenant' => tenant('id'),
            'count' => $products->count()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
            'tenant' => tenant('id')
        ], 201);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        return response()->json([
            'product' => $product,
            'tenant' => tenant('id')
        ]);
    }
}