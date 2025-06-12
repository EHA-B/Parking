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
use App\Models\ParkingStatusHistory;

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
    // ... existing code ...
    public function oldCustomer(Request $request)
    {
        // Find the existing customer
        $customer = Customer::findOrFail($request->customer_id);

        // Create a new vehicle for the existing customer
        $vic = Vic::find($request->vehicle_choose);

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
            'price' => $request->manual_rate ? $request->manual_rate : null,
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null,
            'notes' => $request->notes ?? " ",
            'parking_type' => $request->parking_type ?? 'hourly'
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
            'price' => $request->manual_rate ? $request->manual_rate : null,
            'vic_id' => $vic->id,
            'parcode' => $parcode,
            'time_in' => Carbon::now(),
            'time_out' => null,
            'notes' => $request->notes ?? " ",
            'parking_type' => $request->parking_type ?? 'hourly'
        ]);

        // Return the parcode with the redirect
        return redirect()->route('dashboard.index')->with('new_parcode', $parcode);
    }

    public function checkout($parcode)
    {
        // Find the specific parking slot
        $parking_slot = ParkingSlot::where('parcode', $parcode)->First();
        $vic_id = $parking_slot->vics->id;

        // Calculate the total time parked
        $time_in = Carbon::parse($parking_slot->time_in);
        $time_out = Carbon::now();
        $duration_minutes = $time_in->diffInMinutes($time_out);
        $duration_hours = $duration_minutes / 60;
        $duration_days = $duration_hours / 24;

        // Find the associated vehicle and customer
        $vic = Vic::with(['services', 'items'])->findOrFail($vic_id);
        $customer = $vic->customer;

        // Get pricing based on vehicle type and parking type
        $price_model = Price::first();
        $is_motorcycle = $vic->typ === 'مركبة صغيرة';

        // Calculate base parking price based on parking type
        $total_price = 0;
        switch ($parking_slot->parking_type) {
            case 'hourly':
                $rate = $is_motorcycle ? $price_model->moto_hourly_rate : $price_model->car_hourly_rate;
                $total_price = $duration_hours * $rate;
                break;
            case 'daily':
                $rate = $is_motorcycle ? $price_model->moto_daily_rate : $price_model->car_daily_rate;
                $total_price = ceil($duration_days) * $rate;
                break;
            case 'monthly':
                $rate = $is_motorcycle ? $price_model->moto_monthly_rate : $price_model->car_monthly_rate;
                $total_price = $rate;
                break;
        }

        // Calculate additional services and items price
        $services_price = $vic->services->sum('cost');
        $items_price = $vic->items->sum(function ($item) {
            return $item->price * $item->pivot->item_quantity;
        });


        // Calculate total price including services and items
        // $price_with_services = $total_price + $services_price + $items_price;

        // Prepare checkout details to pass to the view
        $checkoutDetails = [
            'customer_name' => $customer->name,
            'vehicle_type' => $vic->typ,
            'vehicle_plate' => $vic->plate,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'duration_minutes' => $duration_minutes,
            'parking_type' => $parking_slot->parking_type,
            'base_parking_price' => $total_price,
            'manual_rate' => $parking_slot->price,
            'services_price' => $services_price,
            'items_price' => $items_price,

            'vic_id' => $vic_id,
            'parking_slot_id' => $parking_slot->id
        ];

        // Return to the dashboard with checkout details
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
    // ... existing code ...
    // Add a new method to handle the final checkout confirmation
    public function confirmCheckout(Request $request)
    {
        $parking_slot = ParkingSlot::findOrFail($request->parking_slot_id);

        // Calculate the total time parked
        $time_in = Carbon::parse($parking_slot->time_in);
        $time_out = Carbon::now();
        $duration_minutes = $time_in->diffInMinutes($time_out);
        $duration_hours = $duration_minutes / 60;
        $duration_days = $duration_hours / 24;

        // Find the associated vehicle and customer
        $vic = Vic::with(['services', 'items'])->findOrFail($request->vic_id);
        $customer = $vic->customer;

        // Get pricing based on vehicle type and parking type
        $price_model = Price::first();
        $is_motorcycle = $vic->typ === 'مركبة صغيرة';

        // Calculate base parking price based on parking type
        $base_parking_price = 0;
        switch ($parking_slot->parking_type) {
            case 'hourly':
                $rate = $is_motorcycle ? $price_model->moto_hourly_rate : $price_model->car_hourly_rate;
                $base_parking_price = ceil($duration_hours) * $rate;
                break;
            case 'daily':
                $rate = $is_motorcycle ? $price_model->moto_daily_rate : $price_model->car_daily_rate;
                $base_parking_price = ceil($duration_days) * $rate;
                break;
            case 'monthly':
                $rate = $is_motorcycle ? $price_model->moto_monthly_rate : $price_model->car_monthly_rate;
                $base_parking_price = $rate;
                break;
        }

        // Use manual price if set (for daily/monthly), otherwise use calculated base price
        $manual_rate = $parking_slot->price;
        $final_base_price = $manual_rate !== null ? $manual_rate : $base_parking_price;

        // Calculate additional services and items price
        $services_price = $vic->services->sum('cost');
        $items_price = $vic->items->sum(function ($item) {
            return $item->price * $item->pivot->item_quantity;
        });

        // Calculate the total price for history
        $total = $final_base_price + $items_price + $services_price;

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

        // Update the parking slot with checkout time
        $parking_slot->update([
            'time_out' => $time_out
        ]);

        // Update customer's total hours
        $customer->increment('hours', $duration_minutes / 60);

        // Create a history record for the parking session
        History::create([
            'customer_name' => $customer->name,
            'vic_typ' => $vic->typ,
            'vic_plate' => $vic->plate,
            'time_in' => $time_in,
            'time_out' => $time_out,
            'duration' => $duration_minutes,
            'price' => $total,
            'services' => $services_and_items ? json_encode($services_and_items) : null,
            'notes' => $parking_slot->notes,
            'parking_type' => $parking_slot->parking_type
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

    public function toggleStatus($parking_slot_id)
    {
        $parking_slot = ParkingSlot::with('vics.customer')->findOrFail($parking_slot_id);

        // Only allow toggling for monthly subscriptions
        if ($parking_slot->parking_type !== 'monthly') {
            return response()->json([
                'success' => false,
                'message' => 'Status toggle is only available for monthly subscriptions'
            ], 400);
        }

        // Toggle the status
        $new_status = $parking_slot->status === 'in' ? 'out' : 'in';
        $parking_slot->update(['status' => $new_status]);

        // Record the status change in history
        ParkingStatusHistory::create([
            'parking_slot_id' => $parking_slot->id,
            'customer_id' => $parking_slot->vics->customer->id,
            'status' => $new_status,
            'changed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'status' => $new_status,
            'message' => 'تم تعديل الحالة بنجاح'
        ]);
    }

    public function viewStatusHistory($customer_id)
    {
        $history = ParkingStatusHistory::with(['parkingSlot.vics'])
            ->where('customer_id', $customer_id)
            ->orderBy('changed_at', 'desc')
            ->get();

        return view('dashboard.status-history', [
            'history' => $history
        ]);
    }
}
