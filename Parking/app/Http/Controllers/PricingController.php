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
        $validatedData = $request->validate([
            'car_price' => 'required|numeric|min:0',
            'moto_price' => 'required|numeric|min:0'
        ]);

        // Find or create the first pricing record
        $pricing = Price::first();
        if (!$pricing) {
            $pricing = new Price();
        }

        $pricing->car_price = $validatedData['car_price'];
        $pricing->moto_price = $validatedData['moto_price'];
        $pricing->save();

        return redirect()->route('pricing.index')->with('success', 'Pricing updated successfully');
    }
}