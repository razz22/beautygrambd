<?php

namespace App\Http\Controllers\Admin\ThirdParty;

use App\Http\Controllers\Controller;
use App\Services\RedxCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedxController extends Controller
{
    /**
     * Get Redx areas
     */
    public function getAreas(Request $request): JsonResponse
    {
        try {
            $postCode = $request->input('post_code');
            $districtName = $request->input('district_name');

            $filters = [];
            if ($postCode) {
                $filters['post_code'] = $postCode;
            }
            if ($districtName) {
                $filters['district_name'] = $districtName;
            }

            $redxService = app(RedxCourierService::class);
            $response = $redxService->getAreas($filters);

            \Illuminate\Support\Facades\Log::info('RedxController getAreas response', ['response' => $response]);

            // Redx API structure usually returns data in 'areas' key or directly
            if (isset($response['areas'])) {
                 return response()->json([
                    'success' => true,
                    'data' => $response['areas']
                ]);
            }
            
            // Check for direct array
            if (isset($response[0]) && is_array($response)) {
                return response()->json([
                    'success' => true,
                    'data' => $response
                ]);
            }

            // If response is the array of areas itself or different structure or error
             return response()->json([
                'success' => true,
                'data' => $response,
                'areas' => $response // Fallback for JS to check both
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
