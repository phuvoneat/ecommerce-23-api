<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all();
        return response()->json([
            'message' => 'Success',
            'data' => $categories
        ]);
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
        ]);

        $category = Category::create($request->all());
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ]);
    }

    public function update(Request $request,$id){
        $category = Category::find($id); // Find the category by id
        if(!$category){
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        $category->update($request->all());
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id){
        $category = Category::find($id); // Find the category by id
        if(!$category){
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
