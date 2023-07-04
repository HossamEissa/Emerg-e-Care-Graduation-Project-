<?php

namespace App\Http\Controllers\Palmer;

use App\Http\Controllers\Controller;
use App\Models\Palmers\Palmer;
use App\Models\RequestsHistory;
use App\Models\Users\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PalmerRequest extends Controller
{
    use responseTrait;

    public function __construct()
    {
        $this->middleware('checkjwtauth:palmer');
    }

    public function showRequest()
    {
        try {
            $id = auth('palmer')->id();
            $requests = RequestsHistory::where('status', 1)->where('palmer_id', $id)->first();
            if ($requests) {
                $patien_data = User::where('id', $requests->user_id)->first();
                $patien_data = get_data_of_user($patien_data, "null");
                $patien_data['distance'] = $requests->distance;
                $patien_data ['reques_id'] = $requests->id;
                return $this->returnData("data", $patien_data);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }

    public function acceptRequest($id)
    {
        try {

            $valid = Validator::make(['id' => $id], [
                'id' => 'required|numeric',
            ]);


            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }
            $request = RequestsHistory::where('id', $id)->where('status', 1)->first();
            if (!$request) {
                return $this->returnError('', "we can't find any request");
            }
            if (!$request->palmer_id === Auth('palmer')->id()) {
                return $this->returnError('', 'You are not autherized');
            }
            $request->update([
                'status' => 2,
            ]);

            $patient_data = User::where('id', $request->user_id)->first();
            $patient_data = get_data_of_user($patient_data, 'null');

            $patient_data['distance'] = $request->distance;
            $patient_data['reques_id'] = $request->id;
            $msg = "Well done you are now on the way to help the patient be careful please ";
            return $this->returnData("data", $patient_data, $msg);

        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }

    public function confirmOrCancel(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:requests_histories,id',
                'status' => 'required|boolean'
            ]);

            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            $requests = RequestsHistory::where('id', $request->id)->first();
            if (!$requests->palmer_id === Auth('palmer')->id()) {
                return $this->returnError('', 'You are not autherized');
            }

            $requests->update([
                'status' => ($request->status ? 3 : 0),
            ]);
            Palmer::where('id', $requests->palmer_id)->update([
                'status' => 0,
            ]);

            if ($request->status) {
                $msg = "Great job !! , Thanks for you effort";
                return $this->returnSuccessMessage($msg);
            } else {
                $msg = "Ohh this request is canceld , you can help another one thank you";
                return $this->returnSuccessMessage($msg);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }

    }
}
