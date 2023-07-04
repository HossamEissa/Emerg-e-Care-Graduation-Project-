<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:api');
    }

    public function profileInfo()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::fromUser($user);
            $data = get_data_of_user($user, $token);
            return $this->returnData("data", $data);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }

    public function updateProfile(Request $request)
    {
        try {

            $validation = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'national_id' => 'numeric|digits:14|unique:users',
                'phone_number' => 'numeric|digits:10|unique:users',
                'gender' => 'integer',
                'age' => 'numeric',
                'email' => 'string|email|max:255|unique:users',
                'profile_image' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            ]);
            if ($validation->fails()) {
                return $this->returnValidationError($validation);
            }
            $user = auth()->user();
            $user_profile_image = $user->profile_image;

            $user->update([
                'name' => $request->name ? $request->name : $user->name,
                'email' => $request->email ? $request->email : $user->email,
                'national_id' => $request->national_id ? $request->national_id : $user->national_id,
                'phone_number' => $request->phone_number ? $request->phone_number : $user->phone_number,
                'gender' => $request->gender ? $request->gender : $user->gender,
                'age' => $request->age ? $request->age : $user->age,
                'password' => $request->password ? $request->password : $user->password,
            ]);

            if ($request->hasFile('profile_image')) {
                $old_path = $user_profile_image;

//                if ($old_path != defautl_path) {
//                    delete_image('user', $old_path);
//                }

                $path = upload_image($request, 'users', 'profile_image', 'user');
                $user->profile_image = $path;
                $user->save();

            } else {

                $user->profile_image = $user->profile_image;
                $user->save();
            }

            $msg = "Your data updated successfully ";
            return $this->returnSuccessMessage($msg);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }


    }

    public function updateLocation(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'latitude' => 'required|numeric|min:-90|max:90',
                'longitude' => 'required|numeric|min:-180|max:180',
            ]);

            if ($validation->fails()) {
                return $this->returnValidationError($validation);
            }

            $user = auth()->user();
            $user->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $msg = "Your data upate successfully ";
            return $this->returnSuccessMessage($msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }
}
