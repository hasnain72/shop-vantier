<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingRateRequest;
use App\Http\Requests\Admin\StoreShippingZoneRequest;
use App\Models\ShippingRate;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::with('rates')->orderBy('name')->get();
        return view('admin.shipping.index', compact('zones'));
    }

    public function storeZone(StoreShippingZoneRequest $request)
    {
        ShippingZone::create($request->validated());
        return back()->with('success', 'Shipping zone created.');
    }

    public function updateZone(Request $request, ShippingZone $zone)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'countries' => 'required|array|min:1',
            'countries.*' => 'string|size:2',
        ]);

        $zone->update($data);
        return back()->with('success', 'Shipping zone updated.');
    }

    public function deleteZone(ShippingZone $zone)
    {
        $zone->delete();
        return back()->with('success', 'Shipping zone deleted.');
    }

    public function storeRate(StoreShippingRateRequest $request, ShippingZone $zone)
    {
        $zone->rates()->create($request->validated());
        return back()->with('success', 'Shipping rate added.');
    }

    public function updateRate(Request $request, ShippingRate $rate)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'rate_type'          => 'required|in:flat,free,price_based,weight_based',
            'price'              => 'required|numeric|min:0',
            'min_order_subtotal' => 'nullable|numeric|min:0',
            'max_order_subtotal' => 'nullable|numeric|min:0',
            'min_weight'         => 'nullable|numeric|min:0',
            'max_weight'         => 'nullable|numeric|min:0',
            'is_active'          => 'boolean',
        ]);

        $rate->update($data);
        return back()->with('success', 'Shipping rate updated.');
    }

    public function deleteRate(ShippingRate $rate)
    {
        $rate->delete();
        return back()->with('success', 'Shipping rate deleted.');
    }
}
