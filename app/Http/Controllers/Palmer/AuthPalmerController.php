<?php

namespace App\Http\Controllers\Palmer;

use App\Http\Controllers\Controller;
use App\Models\Palmers\Palmer;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthPalmerController extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:palmer', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'national_id' => 'required|numeric|digits:14',
                'password' => 'required|string',
            ]);
            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            $credentials = $request->only('national_id', 'password');

            if (!Auth::guard('palmer')->attempt($credentials)) {
                $msg = "Your national_id or password are not correct ";
                return $this->returnError('000', $msg);
            }

            $palmer = Auth::guard('palmer')->user();
            $token = JWTAuth::fromUser($palmer);
            $msg = "Login Successfully , Happy to see you again ";
            $data = get_data_of_palmer($palmer ,$token);
            return $this->returnData("data", $data, $msg);


        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }

    public function register(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:palmers',
                'national_id' => 'required|numeric|digits:14|unique:palmers',
                'phone_number' => 'required|numeric|digits:10|unique:palmers',
                'government' => 'required',
                'city' => 'required',
                'unit_name' => 'required',
                'car_number' => 'required|numeric',
                'status' => 'numeric',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|min:8'
            ]);

            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            $palmer = Palmer::create([
                'name' => $request->name,
                'email' => $request->email,
                'national_id' => $request->national_id,
                'phone_number' => $request->phone_number,
                'government' => $request->government,
                'city' => $request->city,
                'address' => $request->has('address') ? $request->address : null,
                'unit_name' => $request->unit_name,
                'car_number' => $request->car_number,
                'status' => $request->has('status') ? $request->status : 0,
                'password' => Hash::make($request->password),
            ]);

            $msg = "Register Successfully ! , You can do your work now ";
            return $this->returnSuccessMessage($msg);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth('palmer')->logout();
            $msg = "You are logged out successfully ";
            return $this->returnSuccessMessage($msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }
}
