<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $products = Product::with(['category', 'user', 'productImages'])->paginate();
      return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(ProductRequest $request): JsonResponse
{$data = $request->validated();
    $data['created_by'] = JWTAuth::user()->id;


    $product = Product::create($data);

   
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public'); // guardas en storage/app/public/products

            $product->productImages()->create([
                'image_path' => '/storage/' . $path
            ]);
        }
    }

  
    $product->load(['category', 'user', 'productImages']);

    return response()->json([
        'success' => true,
        'message' => 'Producto creado correctamente',
        'product' => new ProductResource($product)
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
          $product->load(['category', 'user', 'productImages']);

    return response()->json(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        if ($request->hasFile('images')) {
     foreach ($request->file('images') as $image) {
        $path = $image->store('products', 'public');
        $product->productImages()->create([
            'image_path' => '/storage/' . $path
        ]);
    }
}
        return response()->json(new ProductResource($product));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Product $product): Response
    {
        $product->delete();

        return response()->noContent();
    }
}
