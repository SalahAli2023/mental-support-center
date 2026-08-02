<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteStatisticResource;
use App\Models\SiteStatistic;
use Illuminate\Http\Request;

class SiteStatisticController extends Controller
{
    /**
     * Public endpoint for frontend.
     */
    public function publicIndex()
    {
        $stats = SiteStatistic::orderBy('display_order')->get();
        return SiteStatisticResource::collection($stats);
    }

    /**
     * List statistics (admin).
     */
    public function index()
    {
        $stats = SiteStatistic::orderBy('display_order')->get();
        return SiteStatisticResource::collection($stats);
    }

    /**
     * Store a new statistic.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => 'required|string|max:100|unique:site_statistics,key',
            'label_ar' => 'required|string|max:255',
            'label_en' => 'required|string|max:255',
            'value' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $stat = SiteStatistic::create($data);

        return new SiteStatisticResource($stat);
    }

    /**
     * Show a single statistic.
     */
    public function show(SiteStatistic $siteStatistic)
    {
        return new SiteStatisticResource($siteStatistic);
    }

    /**
     * Update an existing statistic.
     */
    public function update(Request $request, SiteStatistic $siteStatistic)
    {
        $data = $request->validate([
            'key' => 'sometimes|string|max:100|unique:site_statistics,key,' . $siteStatistic->id,
            'label_ar' => 'sometimes|string|max:255',
            'label_en' => 'sometimes|string|max:255',
            'value' => 'sometimes|integer|min:0',
            'icon' => 'nullable|string|max:255',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $siteStatistic->update($data);

        return new SiteStatisticResource($siteStatistic);
    }

    /**
     * Delete a statistic.
     */
    public function destroy(SiteStatistic $siteStatistic)
    {
        $siteStatistic->delete();

        return response()->json(['message' => 'Statistic deleted successfully']);
    }
}




