<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginUserRequest;
use App\Http\Requests\Users\RegisterUserRequest;
use App\Models\Users\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthUserController extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'phone_number' => 'required|numeric|digits:10',
                'password' => 'required|string',
            ]);

            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            $credentials = $request->only('phone_number', 'password');
            if (!auth::attempt($credentials)) {
                $msg = "Your Phone number or password are not correct ";
                return $this->returnError('000', $msg);
            }


            $user = Auth::user();
            $token = JWTAuth::fromUser($user);
            $msg = "Login Successfully , Happy to see you again ";
            $data = get_data_of_user($user, $token);
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
                'national_id' => 'required|numeric|digits:14|unique:users',
                'phone_number' => 'required|numeric|digits:10|unique:users',
                'gender' => 'required|integer',
                'age' => 'required|numeric',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|min:8'
            ]);

            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'national_id' => $request->national_id,
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'age' => $request->age,
                'password' => Hash::make($request->password),
            ]);
            $msg = "Register Successfully ! , Don't worry we are here to help you ";
            return $this->returnSuccessMessage($msg);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }


    }

    public function logout(Request $request)
    {
        try {
            auth()->logout();
            $msg  = "You are logged out successfully ";
            return $this->returnSuccessMessage($msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }


}
