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
            'vics.services' => function ($query) {
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


        if ($request->vehicle_choose == "add_vic") {
            $vic = Vic::create([
                'typ' => $request->vehicle_type,
                'brand' => $request->brand,
                'plate' => $request->plate,
                'customer_id' => $customer->id
            ]);

        }
        $parcode = $customer->id . $vic->id . $vic->plate;

        // Create a new parking slot entry
        $parking = ParkingSlot::create([
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null,
            'notes' => $request->notes ?? " "
        ]);


        // Return the parcode with the redirect
        return redirect()->route('dashboard.index')->with('new_parcode', $parcode);
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

        $parcode = $customer->id . $vic->id . $vic->plate;

        $parking = ParkingSlot::create([

            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null,
            'notes' => $request->notes ?? " "
        ]);

        // Return the parcode with the redirect
        return redirect()->route('dashboard.index')->with('new_parcode', $parcode);
    }

    public function checkout($parcode)
    {
        // Find the specific parking slot

        $parking_slot = ParkingSlot::where('parcode', $parcode)->First();
        // dd($parking_slot);
        $vic_id = $parking_slot->vics->id;
        // Calculate the total time parked
        $time_in = Carbon::parse($parking_slot->time_in);
        $time_out = Carbon::now();
        $duration_minutes = $time_in->diffInMinutes($time_out);

        // Find the associated vehicle and customer
        $vic = Vic::with(['services', 'items'])->findOrFail($vic_id);
        $customer = $vic->customer;

        // Get pricing based on vehicle type
        $price_model = Price::first(); // Assuming there's only one pricing record
        $price_per_minute = ($vic->typ === 'مركبة صغيرة')
            ? $price_model->moto_price
            : $price_model->car_price;

        // Calculate base parking price
        $total_price = $duration_minutes * $price_per_minute;

        // Calculate additional services and items price
        $services_price = $vic->services->sum('cost');
        $items_price = $vic->items->sum(function ($item) {
            return $item->price * $item->pivot->item_quantity;
        });

        // Calculate total price including services and items
        $price_with_services = $total_price + $services_price + $items_price;

        // Prepare checkout details to pass to the view
        $checkoutDetails = [
            'customer_name' => $customer->name,
            'vehicle_type' => $vic->typ,
            'vehicle_plate' => $vic->plate,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'duration_minutes' => $duration_minutes,
            'base_parking_price' => $total_price,
            'services_price' => $services_price,
            'items_price' => $items_price,
            'total_price' => $price_with_services,
            'vic_id' => $vic_id,
            'parking_slot_id' => $parking_slot->id
        ];

        // Instead of immediately checking out, return to the dashboard with checkout details
        return view('dashboard.index', [
            'customers' => Customer::with('vics')->get(),
            'parking_slots' => ParkingSlot::with([
                'vics.customer',
                'vics.services' => function ($query) {
                    $query->withPivot('parking_slot_id');
                }
            ])->get(),
            'services' => Service::all(),
            'items' => Item::all(),
            'checkoutDetails' => $checkoutDetails
        ]);
    }

    // Add a new method to handle the final checkout confirmation
    public function confirmCheckout(Request $request)
    {
        $parking_slot = ParkingSlot::findOrFail($request->parking_slot_id);

        // Calculate the total time parked
        $time_in = Carbon::parse($parking_slot->time_in);
        $time_out = Carbon::now();
        $duration_minutes = $time_in->diffInMinutes($time_out);

        // Find the associated vehicle and customer
        $vic = Vic::with('services')->findOrFail($request->vic_id);
        $customer = $vic->customer;

        // Get pricing based on vehicle type
        $price_model = Price::first(); // Assuming there's only one pricing record
        $price_per_minute = ($vic->typ === 'مركبة صغيرة')
            ? $price_model->moto_price
            : $price_model->car_price;

        // Calculate total price
        $total_price = $duration_minutes * $price_per_minute;

        // Update the parking slot with checkout time
        $parking_slot->update([
            'time_out' => $time_out
        ]);

        // Update customer's total hours
        $customer->increment('hours', $duration_minutes / 60);

        // Prepare services and items for JSON storage
        $services_and_items = [];

        // Add services
        if (!$vic->services->isEmpty()) {
            $services = $vic->services->map(function ($service) {
                return [
                    'type' => 'service',
                    'name' => $service->name,
                    'price' => $service->cost
                ];
            })->toArray();
            $services_and_items = array_merge($services_and_items, $services);
        }

        // Add items
        if (!$vic->items->isEmpty()) {
            $items = $vic->items->map(function ($item) {
                return [
                    'type' => 'item',
                    'name' => $item->item,
                    'quantity' => $item->pivot->item_quantity,
                    'price' => $item->price * $item->pivot->item_quantity
                ];
            })->toArray();
            $services_and_items = array_merge($services_and_items, $items);
        }

        // Create a history record for the parking session
        History::create([
            'customer_name' => $customer->name,
            'vic_typ' => $vic->typ,
            'vic_plate' => $vic->plate,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'duration' => $duration_minutes,
            'price' => $total_price,
            'services' => $services_and_items ? json_encode($services_and_items) : null,
            'notes' => $parking_slot->notes
        ]);

        $parking_slot->delete();

        // Redirect back to the dashboard
        return redirect()->route('dashboard.index')->with('success', 'Checkout completed successfully');
    }

    /**
     * Check if a parking code exists
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkParcode(Request $request)
    {
        $parcode = $request->query('parcode');

        // Check if the parking code exists in the database
        $exists = ParkingSlot::where('parcode', $parcode)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function add_service(Request $request, $vic_id, $parking_slot_id)
    {
        $serviceSelect = $request->service_select;
        $itemSelect = $request->item_select;

        // Ensure at least one of service or item is selected
        if ($serviceSelect == 'choose' && $itemSelect == 'choose') {
            return redirect()->back()->with('error', 'Please select a service or an item');
        }

        // If an item is selected, update the item quantity
        if ($itemSelect != 'choose') {
            $item = Item::findOrFail($itemSelect);

            // Check if requested quantity is available
            if ($request->item_quantity > $item->quantity) {
                return redirect()->back()->with('error', 'Insufficient item quantity');
            }

            // Reduce the item quantity
            $item->decrement('quantity', $request->item_quantity);
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
