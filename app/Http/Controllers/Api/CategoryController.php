<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $login_user = $request->user();
        $categories =  Category::with('subcategories')->where('user_id', $login_user->id)->get();
        if ($categories->count() > 0) {

            $categories->transform(function ($category) {
                $category->image = url($category->image);
                $category->subcategories->transform(function ($subcategory) {
                    $subcategory->image = url($subcategory->image);
                    return $subcategory;
                });

                if ($category->count() < 0) {
                    $category->subcategories = $category->subcategories;
                } else {
                    unset($category->subcategories);
                    $category->subcategories = null;
                }
                return $category;
            });
            return response()->json(['status' => 1,  'data' => $categories], 200);
        } else {
            return response()->json(['status' => 1,  'data' =>  null], 200);
        }
    }

    public function storeCategory(Request $request)
    {

        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,NULL,id,user_id,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.unique' => 'The category name has already been taken.',
            'name.max' => 'The category name must not exceed 50 characters.',
        ]);

        if ($request->hasFile('image')) {
            $categoryFile = $request->file('image');
            $categoryFileName = time() . '_' . $categoryFile->getClientOriginalName();
            $categoryFile->move(public_path('images/category/'), $categoryFileName);
            $categoryFileNameAdd = 'images/category/' .  $categoryFileName;
        } else {
            $categoryFileNameAdd = Null;
        }

        $category = new Category();
        $category->name = $request->name;
        $category->image = $categoryFileNameAdd;
        $category->user_id = $user->id;
        $category->save();

        $category->image = url($category->image);

        return response()->json(['status' => 1, 'message' => 'Category Added Successfully',  'data' => $category], 200);
    }


    public function updateCategory(Request $request, $id)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name,' . $id . ',id,user_id,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.unique' => 'The category name has already been taken.',
            'name.max' => 'The category name must not exceed 50 characters.',
        ]);

        $category = Category::findOrFail($id);

        // Check if user has permission to update this category
        if ($category->user_id != $user->id) {
            return response()->json(['status' => 0, 'message' => 'Unauthorized'], 401);
        }

        if ($request->hasFile('image')) {
            $categoryFile = $request->file('image');
            $categoryFileName = time() . '_' . $categoryFile->getClientOriginalName();
            $categoryFile->move(public_path('images/category/'), $categoryFileName);
            $categoryFileNameAdd = 'images/category/' .  $categoryFileName;

            if ($category->image) {
                unlink(public_path($category->image));
            }
        } else {
            $categoryFileNameAdd = $category->image;
        }

        $category->name = $request->name;
        $category->image = $categoryFileNameAdd;
        $category->save();

        $category->image = url($category->image);

        return response()->json(['status' => 1, 'message' => 'Category Updated Successfully', 'data' => $category], 200);
    }

    public function deleteCategory(Request $request, $id)
    {
        try {
            $user = $request->user();
            $category = Category::findOrFail($id);

            if ($category->user_id != $user->id) {
                return response()->json(['status' => 0, 'message' => 'Unauthorized'], 401);
            }

            $subcategories = Subcategory::where('category_id', $id)->get();
            foreach ($subcategories as $subcategory) {
                $subcategory->delete();
                if ($subcategory->image) {
                    unlink(public_path($subcategory->image));
                }
            }
            if ($category->image) {
                unlink(public_path($category->image));
            }

            $category->delete();

            return response()->json(['status' => 1, 'message' => 'Category Deleted Successfully', 'data' => $category], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 0, 'message' => 'Category with the provided ID was not found'], 404);
        }
    }

    public function getSubcategories(Request $request, $id)
    {

        $login_user = $request->user();
        $subcategories = Subcategory::where('category_id', $id)->get();
        if ($subcategories->count() > 0) {
            $subcategories->transform(function ($subcategory) {
                $subcategory->image = url($subcategory->image);
                return $subcategory;
            });
            return response()->json(['status' => 1,  'data' => $subcategories], 200);
        } else {
            return response()->json(['status' => 0,  'data' =>  null], 200);
        }
    }
    public function storeSubcategory(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:50|unique:subcategories,name,NULL,id,category_id,' . $request->category_id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);


        if ($request->hasFile('image')) {
            $subcategoryFile = $request->file('image');
            $subcategoryFileName = time() . '_' . $subcategoryFile->getClientOriginalName();
            $subcategoryFile->move(public_path('images/subcategory/'), $subcategoryFileName);
            $subcategoryFileNameAdd = 'images/subcategory/' .  $subcategoryFileName;
        } else {
            $subcategoryFileNameAdd = Null;
        }
        $subcategory = new Subcategory();
        $subcategory->name = $request->name;
        $subcategory->image = $subcategoryFileNameAdd;
        $subcategory->category_id = $request->category_id;
        $subcategory->save();
        $subcategory->image = url($subcategory->image);
        return response()->json(['status' => 1, 'message' => 'Subcategory Added Successfully', 'data' => $subcategory], 200);
    }

    public function deleteSubcategory(Request $request, $id)
    {
        try {
            $user = $request->user();
            $subcategory = Subcategory::findOrFail($id);
            if ($subcategory->category->user_id != $user->id) {
                return response()->json(['status' => 0, 'message' => 'Unauthorized'], 401);
            }

            if ($subcategory->image) {
                unlink(public_path($subcategory->image));
            }
            $subcategory->delete();

            return response()->json(['status' => 1, 'message' => 'Subcategory Deleted Successfully', 'data' => $subcategory], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 0, 'message' => 'Subcategory with the provided ID was not found'], 404);
        }
    }
}
