<?php

namespace App\Http\Controllers\Admin\ThirdParty;

use App\Http\Controllers\Controller;
use App\Services\SteadfastCourierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SteadfastController extends Controller
{
    public function getDeliveryStatus(Request $request): JsonResponse
    {
        try {
            $consignmentId = $request->input('consignment_id');

            if (!$consignmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consignment ID is required'
                ], 400);
            }

            $steadfastService = app(SteadfastCourierService::class);
            $response = $steadfastService->getDeliveryStatus($consignmentId);

            if (isset($response['status']) && $response['status'] == 200) {
                return response()->json([
                    'success' => true,
                    'data' => $response
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracking details',
                'debug' => $response
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
