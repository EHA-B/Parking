<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ParkingSlot;
use App\Models\Vic;
use App\Models\History;
use App\Models\Item;
use App\Models\Price;
use App\Models\Service;
use App\Models\VicService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $customers = Customer::with('vics')->get();
        $parking_slots = ParkingSlot::with([
            'vics.customer', 
            'vics.services' => function($query) {
                $query->withPivot('parking_slot_id');
            }
        ])->get();

        $services = Service::all();
        $items = Item::all();

        return view('dashboard.index', [
            'customers' => $customers, 
            'parking_slots' => $parking_slots,
            'services' => $services,
            'items' => $items
        ]);
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
      
        $vic = Vic::find($request->vehicle_choose);

        // Generate a unique parking code
       

        if($request->vehicle_choose == "add_vic")
        {
            $vic = Vic::create([
                'typ' => $request->vehicle_type,
                'brand' => $request->brand,
                'plate' => $request->plate,
                'customer_id' => $customer->id
            ]);

        }
        $parcode = $customer->id . $vic->id . $request->plate;

        // Create a new parking slot entry
        $parking = ParkingSlot::create([
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null,
            'notes' => $request->notes ?? " "
        ]);


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
            'time_out' => null,
            'notes' => $request->notes ?? " "
        ]);

        return redirect()->route('dashboard.index');
    }

public function checkout($vic_id, $parking_slot_id)
{
    // Find the specific parking slot
    $parking_slot = ParkingSlot::findOrFail($parking_slot_id);

    // Calculate the total time parked
    $time_in = Carbon::parse($parking_slot->time_in);
    $time_out = Carbon::now();
    $duration_minutes = $time_in->diffInMinutes($time_out);

    // Update the parking slot with checkout time
    $parking_slot->update([
        'time_out' => $time_out
    ]);

    // Find the associated vehicle and customer
    $vic = Vic::with('services')->findOrFail($vic_id);
    $customer = $vic->customer;

    // Update customer's total hours
    $customer->increment('hours', $duration_minutes / 60);

    // Get pricing based on vehicle type
    $price_model = Price::first(); // Assuming there's only one pricing record
    $price_per_minute = ($vic->typ === 'مركبة صغيرة') 
        ? $price_model->moto_price 
        : $price_model->car_price;

    // Calculate total price
    $total_price = $duration_minutes * $price_per_minute;

    // Prepare services for JSON storage
    $services = $vic->services->isEmpty() 
        ? null 
        : json_encode($vic->services->map(function ($service) {
            return [
                'name' => $service->name,
                'price' => $service->cost
            ];
        })->toArray());

    // Create a history record for the parking session
    History::create([
        'customer_name' => $customer->name,
        'vic_typ' => $vic->typ,
        'vic_plate' => $vic->plate,
        'time_in' => $time_in,
        'time_out' => $time_out,
        'duration' => $duration_minutes,
        'price' => $total_price,
        'services' => $services,
        'notes' => $parking_slot->notes // Store services as JSON string
    ]);

    $parking_slot->delete();
    // Redirect back to the dashboard
    return redirect()->route('dashboard.index')->with('success', 'Checkout completed successfully');
}

public function add_service(Request $request, $vic_id, $parking_slot_id)
{
    $serviceSelect = $request->service_select;
    $itemSelect = $request->item_select;

    // Ensure at least one of service or item is selected
    if ($serviceSelect == 'choose' && $itemSelect == 'choose') {
        return redirect()->back()->with('error', 'Please select a service or an item');
    }

    // Create VicService record
    $vicService = VicService::create([
        'service_id' => $serviceSelect != 'choose' ? $serviceSelect : null,
        'vic_id' => $vic_id,
        'item_id' => $itemSelect != 'choose' ? $itemSelect : null,
        'parking_slot_id' => $parking_slot_id,
        'item_quantity' => $request->item_quantity
    ]);

    return redirect()->back()->with('success', 'Service or item added successfully');
}


}
