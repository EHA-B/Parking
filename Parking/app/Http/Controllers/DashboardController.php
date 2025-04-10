<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ParkingSlot;
use App\Models\Vic;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $customers = Customer::with('vics')->get();
        $parking_slots = ParkingSlot::with(['vics.customer','vics.services'])->get();

        return view('dashboard.index', ['customers' => $customers, 'parking_slots' => $parking_slots]);
    }

 // Method to get vehicle types for a specific customer
 public function getCustomerVehicles($customerId)
 {
     $customer = Customer::findOrFail($customerId);
     
     // Assuming the relationship is named 'vehicles'
     $vehicles = $customer->vics;
     
     return response()->json($vehicles);
 }

    public function oldCustomer(Request $request)
    {
        // Find the existing customer
        $customer = Customer::findOrFail($request->customer_id);

        // Create a new vehicle for the existing customer
      
        $vic = Vic::find($request->vehicle_type);

        // Generate a unique parking code
        $parcode = $customer->id . $vic->id . $request->plate;

        // Create a new parking slot entry
        $parking = ParkingSlot::create([
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null
        ]);

        // Optional: You might want to update customer hours or create a history entry
        // For example, creating a history entry
        // History::create([
        //     'customer_name' => $customer->name,
        //     'vic_typ' => $vic->typ,
        //     'vic_plate' => $vic->plate,
        //     'time_in' => Carbon::now(),
        //     'time_out' => null,
        //     'price' => 0 // You might want to calculate this dynamically
        // ]);

        return redirect()->route('dashboard.index');
    }

    public function newCustomer(Request $request)
    {
   
        $customer = Customer::create([

            'name' => $request->name,
            'phone' => $request->phone,
            'hours' => 0
        ]);

        

        $vic = Vic::create([
            'typ' => $request->vehicle_type,
            'brand' => $request->brand,
            'plate' => $request->plate,
            'customer_id' => $customer->id
        ]);

        $parcode = $customer->id.$vic->id.$request->plateInput;

        $parking = ParkingSlot::create([
            
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null
        ]);

        return redirect()->route('dashboard.index');
    }




}
