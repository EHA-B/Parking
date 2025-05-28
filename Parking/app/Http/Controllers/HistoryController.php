<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $filteredHistories = $this->getFilteredHistories($request);

        // Parse services for each history record
        $filteredHistories->transform(function ($history) {
            // Safely decode services, defaulting to an empty array if null or invalid
            $history->parsed_services = $history->services
                ? json_decode($history->services, true)
                : [];

            // Calculate total additional cost from services and items
            $history->additional_cost = $this->calculateAdditionalCost($history->parsed_services);

            return $history;
        });

        return view('history.index', compact('filteredHistories'));
    }

    /**
     * Calculate total additional cost from services and items
     */
    private function calculateAdditionalCost($services)
    {
        if (empty($services)) {
            return 0;
        }

        return array_reduce($services, function ($carry, $item) {
            // Only add cost if it exists and is numeric
            $cost = isset($item['price']) ? floatval($item['price']) : 0;
            return $carry + $cost;
        }, 0);
    }

    private function getFilteredHistories(Request $request)
    {
        // Get query parameters for filtering
        $query = History::query();

        // Filter by customer name if provided
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        // Filter by vehicle type if provided
        if ($request->filled('vic_typ')) {
            $query->where('vic_typ', $request->vic_typ);
        }

        // Filter by parking type if provided
        if ($request->filled('parking_type')) {
            $query->where('parking_type', $request->parking_type);
        }

        // Date range filtering
        if ($request->filled('start_date')) {
            $startDate = $request->start_date . ' 00:00:00';
            $query->where('time_in', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = $request->end_date . ' 23:59:59';
            $query->where('time_in', '<=', $endDate);
        }

        // Order by most recent first
        $filteredHistories = $query->orderByDesc('time_in')->paginate(10);

        return $filteredHistories;
    }

    public function generateReport(Request $request)
    {
        $filteredHistories = $this->getFilteredHistories($request);

        $price_sum = 0;
        $price_with_services = 0;
        $sum_service = 0;
        foreach ($filteredHistories as $history) {
            $services = json_decode($history->services, true);

            if ($history->services) {
                foreach ($services as $service) {
                    $sum_service += $service['price'];
                }


            }
            $price_sum += $history->price;
            $price_with_services = $price_sum + $sum_service;
        }

        // Return the report view with the necessary data
        return view('history.report', ['filteredHistories' => $filteredHistories, 'price_sum' => $price_sum, 'price_with_services' => $price_with_services]);
    }
}