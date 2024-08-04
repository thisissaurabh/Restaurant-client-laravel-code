<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addon;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $login_user = $request->user();

        $addons = Addon::where('user_id', $login_user->id)->get();

        if ($addons->isEmpty()) {
            return response()->json(['status' => 0, 'message' => 'No addons found'], 200);
        }
        $addons = $addons->transform(function ($addon) {
            $addon->image = url($addon->image);
            return $addon;
        });

        return response()->json(['status' => 1, 'data' => $addons], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $login_user = $request->user();
        $request->validate([

            'name' => 'required|string|unique:addons,name,NULL,id,user_id,' . $login_user->id,
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $addon = new Addon();
            $addon->user_id =  $login_user->id;
            $addon->name = $request->name;
            $addon->price = $request->price;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/addons/'), $imageName);
                $addon->image = 'images/addons/' .  $imageName;
            }

            $addon->save();
            $addon->image = url($addon->image);

            return response()->json(['status' => 1, 'message' => 'Addon created successfully', 'data' => $addon], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $login_user = $request->user();
        $addon = Addon::where('id', $id)->where('user_id', $login_user->id)->first();

        if (!$addon) {
            return response()->json(['status' => 0, 'message' => 'Addon not found'], 404);
        }

        $addon->image = url($addon->image);
        return response()->json(['status' => 1, 'data' => $addon], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string',
            'price' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $login_user = $request->user();
            $addon = Addon::where('id', $id)->where('user_id', $login_user->id)->first();

            if (!$addon) {
                return response()->json(['status' => 0, 'message' => 'Addon not found'], 404);
            }

            if ($request->has('name')) {
                $addon->name = $request->name ?? $addon->name;
            }

            if ($request->has('price')) {
                $addon->price = $request->price ?? $addon->price;
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/addons/'), $imageName);
                $addon->image = 'images/addons/' .  $imageName;
                if ($addon->image && $request->hasFile('image')) {
                    if (file_exists(public_path($addon->image))) {
                        unlink(public_path($addon->image));
                    }
                }
            }

            $addon->save();

            $addon->image = url($addon->image);

            return response()->json(['status' => 1, 'message' => 'Addon updated successfully', 'data' => $addon], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $login_user = $request->user();
            $addon = Addon::where('id', $id)->where('user_id', $login_user->id)->first();

            if (!$addon) {
                return response()->json(['status' => 0, 'message' => 'Addon not found'], 404);
            }

            if ($addon->image && file_exists(public_path($addon->image))) {
                unlink(public_path($addon->image));
            }
            $addon->delete();
            return response()->json(['status' => 1, 'message' => 'Addon soft deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
