<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\ProductImageRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductImageResource;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productImages = ProductImage::paginate();

        return ProductImageResource::collection($productImages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductImageRequest $request): JsonResponse
    {
        $productImage = ProductImage::create($request->validated());

        return response()->json(new ProductImageResource($productImage));
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImage $productImage): JsonResponse
    {
        return response()->json(new ProductImageResource($productImage));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductImageRequest $request, ProductImage $productImage): JsonResponse
    {
        $productImage->update($request->validated());

        return response()->json(new ProductImageResource($productImage));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(ProductImage $productImage): Response
    {
        $productImage->delete();

        return response()->noContent();
    }
}
