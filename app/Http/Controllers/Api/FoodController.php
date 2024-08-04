<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use Illuminate\Database\QueryException;
use App\Models\Variation;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        $foodType = $request->input('food_type');
        $searchFoodName = $request->input('search_food_name');
        $foodQuery = Food::query();
        if ($foodType == 'non-veg' || $foodType == 'veg') {
            $foodQuery->where('food_type', $foodType);
        }
        if ($searchFoodName) {
            $foodQuery->where('food_name', 'like', '%' . $searchFoodName . '%');
        }

        if ($foodType == 'all') {
            $foods = $foodQuery->latest()
                ->with(['category', 'subCategory'])
                ->where('user_id', $user->id)
                ->paginate(10);
        } else {
            $foods = $foodQuery->latest()
                ->with(['category', 'subCategory'])
                ->where('user_id', $user->id)
                ->paginate(10);
        }

        $foods->getCollection()->transform(function ($food) {
            if (!empty($food->food_image)) {
                $food->food_image = url($food->food_image);
            }
            $food->category_name = $food->category->name ?? null;
            $food->subcategory_name = $food->subCategory->name ?? null;
            unset($food->category, $food->subCategory);
            return $food;
        });
        return response()->json(['status' => 1, 'fooddata' => $foods->isEmpty() ? null : $foods], 200);
    }

    public function storeFood(Request $request)
    {
        try {
            $user = $request->user();
            $validater =  $request->validate([
                'food_name' => 'required|string|max:255|unique:foods',
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'discount_type' => 'nullable|in:amount,percent',
                'food_type' => 'required|in:non-veg,veg',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:subcategories,id',
                'tag' => 'nullable|string|max:255',
                'max_order_quantity' => 'nullable|integer',
                'food_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variation_name' => 'nullable|string',
                'select_type' => 'nullable|in:s,m',
                'min' => 'nullable|numeric',
                'max' => 'nullable|numeric',
                'variations' => 'nullable|array',
                'variations.*.option_name' => 'required|string',
                'variations.*.additional_price' => 'required|numeric',
            ]);

            // print_r($validater);
            // die;

            if ($request->hasFile('food_image')) {
                $image = $request->file('food_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/foods'), $imageName);
                $foodImage = 'images/foods/' . $imageName;
            } else {
                $foodImage = null;
            }

            // Insert food item
            $food = new Food();
            $food->user_id = $user->id;
            $food->food_name = $request->food_name;
            $food->description = $request->description;
            $food->price = $request->price;
            $food->discount = $request->discount;
            $food->discount_type = $request->discount_type;
            $food->food_type = $request->food_type;
            $food->category_id = $request->category_id;
            $food->sub_category_id = $request->sub_category_id;
            $food->tag = $request->tag;
            $food->max_order_quantity = $request->max_order_quantity;
            $food->food_image = $foodImage;
            $food->save();
            if ($request->has('variations')) {
                foreach ($request->variations as $variation) {
                    Variation::create([
                        'food_id' => $food->id,
                        'option_name' => $variation['option_name'],
                        'additional_price' => $variation['additional_price'],
                    ]);
                }
            }

            $foodAdd =   Food::with('variations')->where('id', $food->id)->first();
            if (!empty($foodAdd->food_image)) {
                $foodAdd->food_image = url($foodAdd->food_image);
            }

            return response()->json(['status' => 1, 'message' => 'Food Added Successfully', 'data' => $foodAdd], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 0, 'message' => 'Query Exception: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function updateFood(Request $request, $id)
    {
        try {
            $user = $request->user();

            $food =  Food::where('id', $id)->where('user_id', $user->id)->firstOrFail();
            $request->validate([
                'food_name' => 'required|string|max:255|unique:foods,food_name,' . $food->id,
                'description' => 'nullable|string',
                'price' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'discount_type' => 'nullable|in:amount,percent',
                'food_type' => 'required|in:non-veg,veg',
                'category_id' => 'required|exists:categories,id',
                'sub_category_id' => 'nullable|exists:subcategories,id',
                'tag' => 'nullable|string|max:255',
                'max_order_quantity' => 'nullable|integer',
                'food_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variation_name' => 'nullable|string',
                'select_type' => 'nullable|in:s,m',
                'min' => 'nullable|numeric',
                'max' => 'nullable|numeric',
                'variations' => 'nullable|array',
                'variations.*.option_name' => 'required|string',
                'variations.*.additional_price' => 'required|numeric',
            ]);

            $oldImage = $food->food_image;

            if ($request->hasFile('food_image')) {
                $image = $request->file('food_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/foods'), $imageName);
                $foodImage = 'images/foods/' . $imageName;
                if (!empty($oldImage) && file_exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }
            } else {
                $foodImage = $food->food_image;
            }

            $food->food_name = $request->food_name;
            $food->description = $request->description;
            $food->price = $request->price;
            $food->discount = $request->discount;
            $food->discount_type = $request->discount_type;
            $food->food_type = $request->food_type;
            $food->category_id = $request->category_id;
            $food->sub_category_id = $request->sub_category_id;
            $food->tag = $request->tag;
            $food->max_order_quantity = $request->max_order_quantity;
            $food->food_image = $foodImage;
            $food->save();


            $food->variations()->delete();
            if ($request->has('variations')) {
                foreach ($request->variations as $variation) {
                    Variation::create([
                        'food_id' => $food->id,
                        'option_name' => $variation['option_name'],
                        'additional_price' => $variation['additional_price'],
                    ]);
                }
            }
            $foodUpdate =   Food::with('variations')->where('id', $food->id)->first();
            if (!empty($foodUpdate->food_image)) {
                $foodUpdate->food_image = url($foodUpdate->food_image);
            }


            return response()->json(['status' => 1, 'message' => 'Food Updated Successfully', 'data' => $foodUpdate], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 0, 'message' => 'Query Exception: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFood(Request $request, $id)
    {
        try {
            $user = $request->user();
            $food = Food::where('id', $id)->where('user_id', $user->id)->first();
            if (!$food) {
                return response()->json(['status' => 0, 'message' => 'Food not found'], 404);
            }
            $food->delete();

            return response()->json(['status' => 1, 'message' => 'Food deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function foodsDetails(Request $request, $id)
    {
        try {
            $user = $request->user();
            $food = Food::with('variations')->where('id', $id)->where('user_id', $user->id)->first();

            if (!empty($food->food_image)) {
                $food->food_image = url($food->food_image);
            }

            if (!$food) {
                return response()->json(['status' => 0, 'message' => 'Food not found'], 404);
            }
            if (count($food->variations) === 0) {
                $variationsData = $food->variations;
                unset($food->variations);
                $food->variations = null;
            }

            return response()->json(['status' => 1, 'message' => 'Food Details', 'data' => $food], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function getItemReport(Request $request)
    {
        $user = $request->user();

        try {
            $sortOrder = $request->input('sort_order', 'desc');
            $sortBy = $request->input('sort_by', 'price');
            $highestSelling = filter_var($request->input('highest_selling', false), FILTER_VALIDATE_BOOLEAN); // Default to false
            $lowestSelling = filter_var($request->input('lowest_selling', false), FILTER_VALIDATE_BOOLEAN); // Default to false

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $perPage = $request->input('per_page', 15);


            $foodQuery = Food::with(['category', 'subCategory'])
                ->where('user_id', $user->id)
                ->withCount(['foodLists as total_quantity_sold' => function ($query) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                    $query->select(DB::raw('SUM(quantity)'));
                }])
                ->withSum(['foodLists as total_amount_sold' => function ($query) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                }], 'total_price');

            // Add date filter to the main query if needed
            if ($startDate && $endDate  && !empty($startDate)  && !empty($endDate)) {
                $foodQuery->whereHas('foodLists', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                });
            }

            // Sorting by price or total discount
            if ($sortBy == 'price') {
                $foodQuery->orderBy('price', $sortOrder);
            } elseif ($sortBy == 'total_discount') {
                $foodQuery->orderBy(DB::raw('price * discount / 100'), $sortOrder);
            }

            // Paginate the results
            $foods = $foodQuery->paginate($perPage);

            // Transform and calculate additional fields
            $foods->getCollection()->transform(function ($food) {
                $totalQuantitySold = $food->total_quantity_sold ?? 0;
                $totalAmountSold = $food->total_amount_sold ?? 0;
                $totalDiscountGiven = $totalQuantitySold * $food->price * ($food->discount / 100);

                return [
                    'id' => $food->id,
                    'name' => $food->food_name,
                    'price' => $food->price,
                    'total_discount_given' => number_format($totalDiscountGiven, 2),
                    'total_quantity_sold' => (int)$totalQuantitySold,
                    'total_amount' => (int)$totalAmountSold,
                    'category_name' => $food->category->name ?? null,
                    'subcategory_name' => $food->subCategory->name ?? null,
                ];
            });


            if ($highestSelling) {
                $foods = $foods->sortByDesc('total_quantity_sold')->values();
            } elseif ($lowestSelling) {
                $foods = $foods->sortBy('total_quantity_sold')->values();
            }

            return response()->json(['status' => 1, 'fooddata' => $foods], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 0, 'message' => 'Query Exception: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
