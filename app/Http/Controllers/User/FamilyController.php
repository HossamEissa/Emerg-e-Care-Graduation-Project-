<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Users\Family;
use App\Models\Users\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FamilyController extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:api');
    }

    public function add_member(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|max:255|string',
                'phone_number' => 'required|numeric|digits:10',
            ]);
            if ($validation->fails()) {
                return $this->returnValidationError($validation);
            }

            $user_id = Auth::id();
            Family::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'kinship' => $request->has('kinship') ? $request->kinship : null,
                'user_id' => $user_id,
            ]);

            $msg = "Your data added successfully ";
            return $this->returnSuccessMessage($msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }


    }

    public function show_members()
    {
        try {
            $user_id = Auth::id();
            $family_member = User::where('id', $user_id)->first()->family_members;

            return $this->returnData("data", $family_member);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }

    public function delete_member($id)
    {
        try {
            $user_id = Auth::id();

            $is_family_member = User::find($user_id)->family_members()->where('id', $id)->exists();

            if (!$is_family_member) {
                $msg = "We can't do it , try again with correct member";
                return $this->returnError('S001', $msg);
            }

            $msg = "Your Family member deleted successfully ";
            return $this->returnSuccessMessage($msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }
}
