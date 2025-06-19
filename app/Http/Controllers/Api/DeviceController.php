<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        $user = $request->user();

        $validator = validator($request->all(), [
            'device_uid' => 'required|string|unique:devices,device_uid',
            'name' => 'nullable|string',
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Provided device_uid or fcm_token already exist or invalid',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $device = Device::create([
                'user_id' => $user->id,
                'device_uid' => $request->device_uid,
                'fcm_token' => $request->fcm_token,
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Device registered successfully',
                'device' => $device,
            ], 201);
        }
    }

}
