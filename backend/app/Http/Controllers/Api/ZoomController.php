<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ZoomController extends Controller
{
    /**
     * إنشاء توقيع Zoom Web SDK.
     */
    public function generateSignature(Request $request)
    {
        $validated = $request->validate([
            'meetingNumber' => 'required|string',
            'role' => 'nullable|integer|in:0,1',
        ]);

        $sdkKey = config('services.zoom.sdk_key', env('ZOOM_SDK_KEY'));
        $sdkSecret = config('services.zoom.sdk_secret', env('ZOOM_SDK_SECRET'));

        if (!$sdkKey || !$sdkSecret) {
            return response()->json([
                'message' => 'Zoom SDK credentials are not configured.'
            ], 500);
        }

        $role = (int) ($validated['role'] ?? 0);
        $meetingNumber = $validated['meetingNumber'];

        // Zoom requires timestamp in milliseconds minus 30s
        $iat = (int) (round(microtime(true) * 1000) - 30000);

        $data = base64_encode($sdkKey . $meetingNumber . $iat . $role);
        $hash = hash_hmac('sha256', $data, $sdkSecret, true);
        $signature = rtrim(strtr(base64_encode($data . '.' . $hash), '+/', '-_'), '=');

        return response()->json([
            'signature' => $signature,
            'sdkKey' => $sdkKey,
            'timestamp' => $iat,
        ]);
    }
}

