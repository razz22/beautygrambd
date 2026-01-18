<?php

namespace App\Http\Controllers\Admin\ThirdParty;

use App\Http\Controllers\Controller;
use App\Services\PathaoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PathaoController extends Controller
{
    /**
     * Get Pathao cities
     */
    public function getCities(): JsonResponse
    {
        try {
            $pathaoService = app(PathaoService::class);
            $response = $pathaoService->getCities();

            // Return full response for debugging
            if (isset($response['data']['data'])) {
                return response()->json([
                    'success' => true,
                    'data' => $response['data']['data']
                ]);
            }

            // Return the actual response to see what's wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities',
                'debug' => $response // Include full response for debugging
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Add trace for debugging
            ], 500);
        }
    }

    /**
     * Get Pathao zones for a specific city
     */
    public function getZones(Request $request): JsonResponse
    {
        try {
            $cityId = $request->input('city_id');

            if (!$cityId) {
                return response()->json([
                    'success' => false,
                    'message' => 'City ID is required'
                ], 400);
            }

            $pathaoService = app(PathaoService::class);
            $response = $pathaoService->getZones($cityId);

            if (isset($response['data']['data'])) {
                return response()->json([
                    'success' => true,
                    'data' => $response['data']['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch zones'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getAreas(Request $request): JsonResponse
    {
        try {
            $zoneId = $request->input('zone_id');

            if (!$zoneId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone ID is required'
                ], 400);
            }

            $pathaoService = app(PathaoService::class);
            $response = $pathaoService->getAreas($zoneId);

            if (isset($response['data']['data'])) {
                return response()->json([
                    'success' => true,
                    'data' => $response['data']['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch areas'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrderDetails(Request $request): JsonResponse
    {
        try {
            $consignmentId = $request->input('consignment_id');

            if (!$consignmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Consignment ID is required'
                ], 400);
            }

            $pathaoService = app(PathaoService::class);
            $response = $pathaoService->getOrderInfo($consignmentId);

            if (isset($response['code']) && $response['code'] == 200) {
                return response()->json([
                    'success' => true,
                    'data' => $response['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details',
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
