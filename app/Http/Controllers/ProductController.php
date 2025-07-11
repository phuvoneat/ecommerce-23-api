<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            // 'image' => 'required',
        ]);

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imagePath = Storage::disk('public')->put('products', $image);
            $request->image = $imagePath;
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'image' => $request->image
        ]);
        $product->image = url(Storage::url($product->image));
        return response()->json($product, 200);
    }
    public function update(Request $request,$id){
        $product = Product::find($id);
        $data = $request->all();
        if(!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imagePath = Storage::disk('public')->put('products', $image);
            $data['image'] = $imagePath;

            // if(Storage::disk('public')->exists($product->image)){
            //     Storage::disk('public')->delete($product->image);
            // }
        }
        $product->update($data);
        return response()->json($product, 200);
    }
    public function destroy($id){
        $product = Product::find($id);
        if(!$product){
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function index(){
        $categories = Category::with('products')->latest()->get(['id', 'name']);
        $groupProducts = $categories->map(function($category){
            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'products' => $category->products->map(function($product){
                    $product->image = url(Storage::url($product->image));
                    return $product;
                })
            ];
        });
        return response()->json($groupProducts, 200);
    }

    public function getProductByCategory($cateId){
        $category = Category::find($cateId); 
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        $products = $category->products->map(function($product){
            $product->image = url(Storage::url($product->image));
            return $product;
        });
        return response()->json($products, 200);
    }
    public function searchProduct(Request $request){
        
        $request->validate([
            'name' => 'required'
        ]);
        $products = Product::where('name', 'like', '%'.$request->name.'%')
        // ->orWhere('description', 'like', '%'.$request->description.'%')
        ->get();
        $products->map(function($product){
            $product->image = url(Storage::url($product->image));
            return $product;
        });
        return response()->json($products, 200);
    }
}
