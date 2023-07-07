<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Palmers\Palmer;
use App\Models\RequestsHistory;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NeedPalmerController extends Controller
{
    use responseTrait;

    public $user;

    public function __construct()
    {
        $this->middleware('checkjwtauth:api');
        $this->user = Auth::user();
    }

    public function needHelp()
    {
        try {

            $userLatitude = $this->user->latitude;
            $userLongitude = $this->user->longitude;

            //            $userLatitude = 37.7749;
            //            $userLongitude = -122.4194;

            $nearestPalmer = DB::table('palmers')
                ->select(
                    'id',
                    'latitude',
                    'longitude',
                    DB::raw('(6371 * acos(cos(radians(' . $userLatitude . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $userLongitude . ')) + sin(radians(' . $userLatitude . ')) * sin(radians(latitude)))) AS distance')
                )->where('status', 0)->orderBy('distance')->limit(10) // Limit the result to the 10 nearest cars
                ->first();

            if ($nearestPalmer) {
                $msg = "We are searching for a palmer to help you";
                Palmer::where('id', $nearestPalmer->id)->update([
                    'status' => 1
                ]);
                $request = RequestsHistory::create([
                    'user_id' => Auth::id(),
                    'palmer_id' => $nearestPalmer->id,
                    'status' => 1,
                    'distance' => $nearestPalmer->distance
                ]);

                $data = Palmer::find($nearestPalmer->id);
                $palmer = get_data_of_palmer($data, "null");
                $palmer['reques_id'] = $request->id;
                $palmer['distance'] = $nearestPalmer->distance;

                return $this->returnData("data", $palmer, $msg);
            } else {
                $msg = "No palmer available, please try again";
                return $this->returnError("", $msg);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }

    public function checkAccept($id)
    {
        try {
            $valid = Validator::make(['id' => $id], [
                'id' => 'required|numeric',
            ]);

            if ($valid->fails()) {
                return $this->returnValidationError($valid);
            }

            $reques_accept = RequestsHistory::where('status', 2)->where('id', $id)->first();
            $palmer = Palmer::find($reques_accept->palmer_id);
            $palmer = get_data_of_palmer($palmer, 'null');
            $palmer['distance'] = $reques_accept->distance;
            $palmer['reques_id'] = $reques_accept->id;
            $msg = "Hi , My Palmer in the way to you , don't worry ";
            return $this->returnData("data", $palmer, $msg);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }

    public function confirmOrCancel(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'id' => 'required|numeric',
                'status' => 'required|boolean',
                'palmer_id' => 'required|exists:palmers,id'
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
                $msg = "You are now in the place that will take care of you";
                return $this->returnSuccessMessage($msg);
            } else {
                $msg = "Ohh Thank you for using me  , I am here to help you ";
                return $this->returnSuccessMessage($msg);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->returnError($error = "", $msg);
        }
    }
}
