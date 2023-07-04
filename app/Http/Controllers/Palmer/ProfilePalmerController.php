<?php

namespace App\Http\Controllers\Palmer;

use App\Http\Controllers\Controller;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfilePalmerController extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:palmer');
    }

    public function profileInfo()
    {
        try {
            $palmer = auth('palmer')->user();
            $token = JWTAuth::fromUser($palmer);
            $data = get_data_of_palmer($palmer, $token);
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
                'national_id' => 'numeric|digits:14|unique:palmers',
                'phone_number' => 'numeric|digits:10|unique:palmers',
                'gender' => 'integer',
                'age' => 'numeric',
                'government' => 'string',
                'city' => 'string',
                'unit_name' => 'integer',
                'car_number' => 'integer',
                'email' => 'string|email|max:255|unique:palmers',
                'profile_image' => 'image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            ]);
            if ($validation->fails()) {
                return $this->returnValidationError($validation);
            }
            $palmer = auth('palmer')->user();
            $palmer_profile_image = $palmer->profile_image;

            $palmer->update([
                'name' => $request->name ? $request->name : $palmer->name,
                'email' => $request->email ? $request->email : $palmer->email,
                'national_id' => $request->national_id ? $request->national_id : $palmer->national_id,
                'phone_number' => $request->phone_number ? $request->phone_number : $palmer->phone_number,
                'gender' => $request->gender ? $request->gender : $palmer->gender,
                'age' => $request->age ? $request->age : $palmer->age,
                'password' => $request->password ? $request->password : $palmer->password,
            ]);

            if ($request->hasFile('profile_image')) {
                $old_path = $palmer_profile_image;

//                if ($old_path !== defautl_path) {
//                    delete_image('palmer', $old_path);
//                }

                $path = upload_image($request, 'palmers', 'profile_image', 'palmer');
                $palmer->profile_image = $path;
                $palmer->save();

            } else {

                $palmer->profile_image = $palmer->profile_image;
                $palmer->save();
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

            $palmer = auth('palmer')->user();
            $palmer->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $msg = "Your data upate successfully ";
            return $this->returnSuccessMessage($msg);
        }catch (\Exception $e){
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }
}
