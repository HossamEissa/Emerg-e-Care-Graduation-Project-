<?php


if (!function_exists('get_data_of_palmer')) {
    function get_data_of_palmer($palmer, $token)
    {
        return [
            'id' => $palmer->id,
            'name' => $palmer->name,
            'email' => $palmer->email,
            'latitude' => $palmer->latitude,
            'longitude' => $palmer->longitude,
            'government' => $palmer->government,
            'city' => $palmer->city,
            'unit_name' => $palmer->unit_name,
            'car_number' => $palmer->car_number,
            'status' => $palmer->status,
            'national_id' => $palmer->national_id,
            'Phone_number' => $palmer->phone_number,
            'profile_image' => asset('images/' . $palmer->profile_image),
            'token' => $token,
        ];
    }
}




