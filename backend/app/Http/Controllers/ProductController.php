<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Validator;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::latest()->paginate(8);

        if (is_null($products->first())) {
            return response()->json([
                'status' => 200,
                'message' => 'No product found!',
            ], 200);
        }

        $response = [
            'status' => 200,
            'message' => 'Products are retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'description' => 'required|string|'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 422);
        }

        $product = Product::create($request->all());

        $response = [
            'status' => 200,
            'message' => 'Product is added successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }


    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 404,
                'message' => 'Product is not found!',
            ], 404);
        }

        $response = [
            'status' => 200,
            'message' => 'Product is retrieved successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }


    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 404,
                'message' => 'Product is not found!',
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:250',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'description' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 422);
        }

        $product->update([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'description' => $request->input('description')
        ]);

        $response = [
            'status' => 200,
            'message' => 'Product is updated successfully.',
            'data' => $product,
        ];

        return response()->json($response, 200);
    }



    public function destroy($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 404,
                'message' => 'Product is not found!',
            ], 404);
        }

        Product::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Product is deleted successfully.'
        ], 200);
    }


    public function search($name)
    {
        $products = Product::where('name', 'like', '%' . $name . '%')
            ->latest()->paginate(8);

        if (is_null($products->first())) {
            return response()->json([
                'status' => 404,
                'message' => 'No product found!',
            ], 404);
        }

        $response = [
            'status' => 200,
            'message' => 'Products are retrieved successfully.',
            'data' => $products,
        ];

        return response()->json($response, 200);
    }
}
