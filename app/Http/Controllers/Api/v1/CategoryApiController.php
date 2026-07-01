<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use OpenApi\Annotations as OA;

class CategoryApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/api-categories",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     summary="Get all categories",
     *     description="Returns list of categories",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Category::all());
    }

    /**
     * @OA\Get(
     *     path="/api/api-categories/{id}",
     *     operationId="getCategoryById",
     *     tags={"Categories"},
     *     summary="Get category by ID",
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
     *         description="Category not found"
     *     )
     * )
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    /**
     * @OA\Post(
     *     path="/api/api-categories",
     *     operationId="createCategory",
     *     tags={"Categories"},
     *     summary="Create category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Laptops"),
     *             @OA\Property(property="slug", type="string", example="laptops"),
     *             @OA\Property(property="description", type="string", example="Laptop computers and accessories"),
     *             @OA\Property(property="image", type="string", example="categories/laptops.jpg")
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
        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/api-categories/{id}",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     summary="Update category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Gaming Laptops"),
     *             @OA\Property(property="slug", type="string", example="gaming-laptops"),
     *             @OA\Property(property="description", type="string", example="High performance laptops for gaming"),
     *             @OA\Property(property="image", type="string", example="categories/gaming-laptops.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->update($request->all());
        return response()->json($category);
    }

    /**
     * @OA\Delete(
     *     path="/api/api-categories/{id}",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     summary="Delete category by ID",
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
     *         description="Category not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
