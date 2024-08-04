<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function register(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'firstName' => 'required',
                'email' => 'required|email|unique:users',
                'phone' => 'required|numeric',
                'password' => 'required|min:6',
                'confirmPassword' => 'required|same:password',
                'lastName' => 'nullable',
                'document' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'logoImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'restCoverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'vatTax' => 'nullable',
                'EstDelTime' => 'nullable|date',
                'restName' => 'nullable',
                'restAddress' => 'nullable',
                'tin' => 'nullable',
                'registerDate' => 'nullable|date',
            ]);
            if ($request->hasFile('logoImage')) {
                $logoFile = $request->file('logoImage');
                $logoFileName = time() . '_' . $logoFile->getClientOriginalName();
                $logoFile->move(public_path('images/restImage/'), $logoFileName);
                $logoFileNameAdd = 'images/restImage/' .  $logoFileName;
            } else {
                $logoFileNameAdd = Null;
            }
            if ($request->hasFile('restCoverImage')) {
                $restCoverFile = $request->file('restCoverImage');
                $restCoverFileName = time() . '_' . $restCoverFile->getClientOriginalName();
                $restCoverFile->move(public_path('images/restImage/'), $restCoverFileName);
                $restCoverImage = 'images/restImage/' . $restCoverFileName;
            } else {
                $restCoverImage = Null;
            }
            if ($request->hasFile('document')) {
                $restdocument = $request->file('document');
                $restdocumentName = time() . '_' . $restdocument->getClientOriginalName();
                $restdocument->move(public_path('images/restImage/'), $restdocumentName);
                $restdocumentImage = 'images/restImage/' . $restdocumentName;
            } else {
                $restdocumentImage = Null;
            }

            $user = new User();
            $user->name = $validatedData['firstName'];
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->password = Hash::make($validatedData['password']);
            $user->lastName = $validatedData['lastName'];
            $user->logoImage = $logoFileNameAdd;
            $user->restCoverImage = $restCoverImage;
            $user->document = $restdocumentImage;
            $user->vatTax = $validatedData['vatTax'];
            $user->EstDelTime = $validatedData['EstDelTime'];
            $user->restName = $validatedData['restName'];
            $user->restAddress = $validatedData['restAddress'];
            $user->tin = $validatedData['tin'];
            $user->registerDate = $validatedData['registerDate'];
            $user->save();

            $token = $user->createToken('authToken')->accessToken;
            $userData = User::find($user->id);
            $userData->profileImage = url($userData->profileImage) ?? null;
            $userData->document = url($userData->document) ?? null;
            $userData->restCoverImage = url($userData->restCoverImage) ?? null;
            $userData->logoImage = url($userData->logoImage) ?? null;
            return response()->json(['status' => 1,  'token' => $token, 'userData' => $userData], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required',
            'password' => 'required|min:6',
        ]);
        try {
            if (!auth()->attempt($loginData)) {
                return response(['status' => 0, 'message' => 'Invalid Credentials'], 401);
            }
            $user = User::where('email', $loginData['email'])->first();
            $token = $user->createToken('authToken')->accessToken;
            return response(['status' => 1, 'token' => $token, 'userData' => $user], 200);
        } catch (\Exception $e) {

            return response(['status' => 0, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function user(Request $request)
    {
        $request->user();

        $user = User::find($request->user()->id);
        $user->profileImage = $user->profileImage ? url($user->profileImage) : null;
        $user->document = url($user->document) ?? null;
        $user->restCoverImage = $user->restCoverImage ? url($user->restCoverImage) : null;
        $user->logoImage = $user->logoImage ? url($user->logoImage) : null;
        return response()->json(['status' => 1, 'userData' => $user], 200);
    }



    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 1,
            'message' => 'You have been successfully logged out!'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $login_user =   $request->user();
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->where('id', $login_user->id)->first();

        if (!$user) {
            return response()->json(['status' => 0, 'message' => 'User not found'], 404);
        }

        $otp = generateOTP();
        $data['otp'] = $otp;
        $data['email'] = $user->email;
        $data['name'] = $user->name;

        Mail::to($user->email)->send(new ForgotPasswordMail($data));
        Cache::put('forgotPasswordOtp', $otp, now()->addMinutes(5));
        Cache::put('forgotPasswordEmail', $user->email, now()->addMinutes(10));
        return response()->json(['status' => 1, 'message' => 'OTP sent to your email', 'OTP' => $otp], 200);
    }

    public function verifyOTP(Request $request)
    {

        $request->validate([
            'otp' => 'required',
        ]);
        $otp = Cache::get('forgotPasswordOtp');
        if ($otp == $request->otp) {
            Cache::forget('forgotPasswordOtp');
            return response()->json(['status' => 1, 'message' => 'OTP verified'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Invalid OTP'], 401);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
            'confirmPassword' => 'required|same:password',
        ]);
        $email = Cache::get('forgotPasswordEmail');
        if (!empty($email)   && isset($email)) {
            $user = User::where('email', $email)->first();
            $user->password =   Hash::make($request->password);
            $user->save();
            Cache::forget('forgotPasswordEmail');
            return response()->json(['status' => 1, 'message' => 'Password reset successfully'], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Something went wrong'], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        $login_user = $request->user();
        $request->validate([
            'name' => 'nullable|string',
            'lastName' => 'nullable|string',
            'phone' => 'nullable|integer',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            if ($request->hasFile('profileImage')) {
                $profileImage = $request->file('profileImage');
                $profileImageName = time() . '_' . $profileImage->getClientOriginalName();
                $profileImage->move(public_path('images/profileImage/'), $profileImageName);
                $profileImageNameAdd = 'images/profileImage/' .  $profileImageName;

                if ($login_user->profileImage) {
                    unlink(public_path($login_user->profileImage));
                }
            } else {
                $profileImageNameAdd = $login_user->profileImage;
            }

            User::where('id', $login_user->id)->update([
                'name' => $request->name ?? $login_user->name,
                'lastName' => $request->lastName ?? $login_user->lastName,
                'phone' => $request->phone ?? $login_user->phone,
                'profileImage' => $profileImageNameAdd,
            ]);

            $updated_user = User::find($login_user->id);
            $updated_user->profileImage = url($updated_user->profileImage);
            $updated_user->logoImage = url($updated_user->logoImage);
            $updated_user->restCoverImage = url($updated_user->restCoverImage);


            return response()->json(['status' => 1, 'message' => 'Profile updated successfully', 'data' => $updated_user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateRestaurant(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'document' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'logoImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'restCoverImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'vatTax' => 'nullable',
                'EstDelTime' => 'nullable|date',
                'restName' => 'nullable',
                'restAddress' => 'nullable',
                'tin' => 'nullable'
            ]);

            $user = $request->user();

            if ($request->hasFile('logoImage')) {
                if (!empty($user->logoImage) && file_exists(public_path($user->logoImage))) {
                    unlink(public_path($user->logoImage));
                }
                $logoFile = $request->file('logoImage');
                $logoFileName = time() . '_' . $logoFile->getClientOriginalName();
                $logoFile->move(public_path('images/restImage/'), $logoFileName);
                $user->logoImage = 'images/restImage/' . $logoFileName;
            }

            if ($request->hasFile('restCoverImage')) {
                if (!empty($user->restCoverImage) && file_exists(public_path($user->restCoverImage))) {
                    unlink(public_path($user->restCoverImage));
                }
                $restCoverFile = $request->file('restCoverImage');
                $restCoverFileName = time() . '_' . $restCoverFile->getClientOriginalName();
                $restCoverFile->move(public_path('images/restImage/'), $restCoverFileName);
                $user->restCoverImage = 'images/restImage/' . $restCoverFileName;
            }

            if ($request->hasFile('document')) {
                if (!empty($user->document) && file_exists(public_path($user->document))) {
                    unlink(public_path($user->document));
                }
                $restdocument = $request->file('document');
                $restdocumentName = time() . '_' . $restdocument->getClientOriginalName();
                $restdocument->move(public_path('images/restImage/'), $restdocumentName);
                $user->document = 'images/restImage/' . $restdocumentName;
            }

            $user->vatTax = $validatedData['vatTax'];
            $user->EstDelTime = $validatedData['EstDelTime'];
            $user->restName = $validatedData['restName'];
            $user->restAddress = $validatedData['restAddress'];
            $user->tin = $validatedData['tin'];
            $user->save();

            $userData = User::find($user->id);
            $userData->profileImage = url($userData->profileImage) ?? null;
            $userData->document = url($userData->document) ?? null;
            $userData->restCoverImage = url($userData->restCoverImage) ?? null;
            $userData->logoImage = url($userData->logoImage) ?? null;
            return response()->json(['status' => 1, 'userData' => $userData], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
