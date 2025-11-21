<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 *
 * @property $id
 * @property $category_id
 * @property $created_by
 * @property $updated_by
 * @property $name
 * @property $description
 * @property $price
 * @property $stock
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @property Category $category
 * @property User $user
 * @property User $user
 
 * @property ProductImage[] $productImages
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Product extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['category_id', 'created_by', 'updated_by', 'name', 'description', 'price', 'stock', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id');
    }
    
  
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productImages()
{
    return $this->hasMany(\App\Models\ProductImage::class, 'product_id', 'id');
}
    
}
