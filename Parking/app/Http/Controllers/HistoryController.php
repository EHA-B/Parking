<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
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

        // Date range filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('time_in', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Order by most recent first
        $histories = $query->orderByDesc('time_in')->paginate(10);

        // Parse services for each history record
        $histories->transform(function ($history) {
            // Safely decode services, defaulting to an empty array if null or invalid
            $history->parsed_services = $history->services 
                ? json_decode($history->services, true) 
                : [];
            
            // Calculate total additional cost from services and items
            $history->additional_cost = $this->calculateAdditionalCost($history->parsed_services);
            
            return $history;
        });

        return view('history.index', compact('histories'));
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
}