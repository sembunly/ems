<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use OpenApi\Annotations as OA;

class ProductApiController extends Controller
{
    // GET all products
    /**
     * @OA\Get(
     *     path="/api/api-products",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     summary="Get all products",
     *     description="Returns list of products",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::all());
    }

    // GET single product
    /**
     * @OA\Get(
     *     path="/api/api-products/{id}",
     *     operationId="getProductById",
     *     tags={"Products"},
     *     summary="Get product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json($product);
    }

    // CREATE product
    /**
     * @OA\Post(
     *     path="/api/api-products",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     summary="Create product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price"},
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Dell Precision"),
     *             @OA\Property(property="brand", type="string", example="Dell"),
     *             @OA\Property(property="model", type="string", example="Precision 5680"),
     *             @OA\Property(property="price", type="number", format="float", example=1200.50),
     *             @OA\Property(property="stock", type="integer", example=10),
     *             @OA\Property(property="image", type="string", example="products/dell-precision.jpg"),
     *             @OA\Property(property="description", type="string", example="Powerful laptop for professional workloads"),
     *             @OA\Property(property="ram", type="string", example="32GB"),
     *             @OA\Property(property="storage", type="string", example="1TB SSD"),
     *             @OA\Property(property="processor", type="string", example="Intel Core i7"),
     *             @OA\Property(property="screen_size", type="string", example="16 inch")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $request->image,
            'description' => $request->description,
            'ram' => $request->ram,
            'storage' => $request->storage,
            'processor' => $request->processor,
            'screen_size' => $request->screen_size,
        ]);

        return response()->json($product, 201);
    }

    // UPDATE product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->update($request->all());

        return response()->json($product);
    }

    // DELETE product
    /**
     * @OA\Delete(
     *     path="/api/api-products/{id}",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     summary="Delete product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
