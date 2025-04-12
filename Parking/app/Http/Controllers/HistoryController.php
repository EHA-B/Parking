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

        return view('history.index', compact('histories'));
    }
}