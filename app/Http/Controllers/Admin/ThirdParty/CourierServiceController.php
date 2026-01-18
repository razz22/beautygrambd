<?php

namespace App\Http\Controllers\Admin\ThirdParty;

use App\Http\Controllers\BaseController;
use App\Models\Courier;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourierServiceController extends BaseController
{
    /**
     * Display the courier service configuration page
     */
    public function index(?Request $request = null, ?string $type = null): View
    {
        $couriers = Courier::orderBy('id', 'desc')->get();
        
        return view('admin-views.third-party.courier-service.index', compact('couriers'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:Fixed,Weight-Based,Distance-Based',
            'min_charge' => 'nullable|numeric|min:0',
            'max_charge' => 'nullable|numeric|min:0',
            'delivery_charge' => 'nullable|numeric|min:0',
            'base_url' => 'nullable|string|max:255',
            'test_base_url' => 'nullable|string|max:255',
        ]);

        $courier = Courier::findOrFail($id);
        
        $courier->update([
            'title' => $request->title,
            'type' => $request->type,
            'min_charge' => $request->min_charge ?? 0,
            'max_charge' => $request->max_charge ?? 0,
            'delivery_charge' => $request->delivery_charge ?? 0,
            'base_url' => $request->base_url,
            'test_base_url' => $request->test_base_url,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'test_client_id' => $request->test_client_id,
            'test_client_secret' => $request->test_client_secret,
            'client_email' => $request->client_email,
            'client_password' => $request->client_password,
            'grant_type' => $request->grant_type ?? 'password',
            'store_id' => $request->store_id,
            'is_live' => $request->has('is_live') ? 1 : 0,
        ]);

        ToastMagic::success(translate('Courier_service_updated_successfully'));
        return back();
    }

    /**
     * Update courier active status
     */
    public function updateStatus(Request $request)
    {
        $courier = Courier::findOrFail($request->id);
        $courier->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => translate('Status_updated_successfully')
        ]);
    }
}
