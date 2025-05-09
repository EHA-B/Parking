<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $pricing = Price::first() ?? new Price();
        return view('pricing.index', compact('pricing'));
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'car_hourly_rate' => 'required|numeric|min:0',
                'car_daily_rate' => 'required|numeric|min:0',
                'car_monthly_rate' => 'required|numeric|min:0',
                'moto_hourly_rate' => 'required|numeric|min:0',
                'moto_daily_rate' => 'required|numeric|min:0',
                'moto_monthly_rate' => 'required|numeric|min:0'
            ]);

            // Find or create the first pricing record
            $pricing = Price::first();
            if (!$pricing) {
                $pricing = new Price();
            }

            $pricing->fill($validatedData);
            $pricing->save();

            return redirect()->back();

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الأسعار'
            ], 500);
        }
    }
}