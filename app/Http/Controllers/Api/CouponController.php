<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\Coupon;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $login_user = $request->user();
        $coupons = Coupon::where('user_id', $login_user->id)->get();
        if ($coupons->isEmpty()) {
            return response()->json(['status' => 0, 'message' => 'No addons found'], 200);
        }
        return response()->json(['status' => 1, 'data' => $coupons], 200);
    }



    public function store(Request $request)
    {
        $login_user = $request->user();
        $request->validate([
            'title' => 'required',
            'code' => 'required|unique:coupons,code,NULL,id,user_id,' . $login_user->id,
            'limitForSameUser' => 'required|integer',
            'MinPurchase' => 'required|numeric',
            'startDate' => 'required|date',
            'expireDate' => 'required|date',
            'discount' => 'required|numeric',
            'discountType' => 'required|in:amount,percent',
            'maxDiscount' => 'nullable|numeric'
        ]);

        $coupon =  new Coupon();
        $coupon->user_id = $login_user->id;
        $coupon->title = $request->title;
        $coupon->code = $request->code;
        $coupon->limitForSameUser = $request->limitForSameUser;
        $coupon->MinPurchase = $request->MinPurchase;
        $coupon->startDate = $request->startDate;
        $coupon->expireDate = $request->expireDate;
        $coupon->discount = $request->discount;
        $coupon->discountType = $request->discountType;
        $coupon->maxDiscount = $request->maxDiscount;
        $coupon->save();

        return response()->json(['success' => 1, 'message' => 'Coupon created successfully.', 'data' => $coupon], 201);
    }


    public function update(Request $request, $id)
    {
        $login_user = $request->user();
        $request->validate([
            'title' => 'required',
            'code' => 'required|unique:coupons,code,' . $id,
            'limitForSameUser' => 'required|integer',
            'MinPurchase' => 'required|numeric',
            'startDate' => 'required|date',
            'expireDate' => 'required|date',
            'discount' => 'required|numeric',
            'discountType' => 'required|in:amount,percent',
            'maxDiscount' => 'nullable|numeric'
        ]);

        $couponsUpdate = Coupon::find($id);
        $couponsUpdate->user_id = $login_user->id;
        $couponsUpdate->title = $request->title;
        $couponsUpdate->code = $request->code;
        $couponsUpdate->limitForSameUser = $request->limitForSameUser;
        $couponsUpdate->MinPurchase = $request->MinPurchase;
        $couponsUpdate->startDate = $request->startDate;
        $couponsUpdate->expireDate = $request->expireDate;
        $couponsUpdate->discount = $request->discount;
        $couponsUpdate->discountType = $request->discountType;
        $couponsUpdate->maxDiscount = $request->maxDiscount;
        $couponsUpdate->save();

        return response()->json(['success' => 1, 'message' => 'Coupon updated successfully.', 'data' =>  $couponsUpdate]);
    }


    public function show(Request $request, $id)
    {
        $login_user = $request->user();
        $coupon = Coupon::where('id', $id)->where('user_id', $login_user->id)->first();
        if (!$coupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon not found'], 404);
        }
        return response()->json(['status' => 1, 'data' => $coupon], 200);
    }

    public function destroy(Request $request, $id)
    {
        $login_user = $request->user();
        $coupon = Coupon::where('id', $id)->where('user_id', $login_user->id)->first();
        if (!$coupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon not found'], 404);
        }
        $coupon->delete();
        return response()->json(['status' => 1, 'message' => 'Addon soft deleted successfully'], 200);
    }
}
