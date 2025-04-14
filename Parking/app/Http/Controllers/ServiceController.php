<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     * Note: This method is typically called from ItemController's index method
     */
    public function index()
    {
        $services = Service::all();
        return view('items-services.index', compact('services'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name',
            'cost' => 'required|numeric|min:0'
        ], [
            // Custom error messages
            'name.unique' => 'هذه الخدمة موجودة بالفعل',
            'name.required' => 'يجب إدخال اسم الخدمة',
            'cost.required' => 'يجب إدخال تكلفة الخدمة',
            'cost.numeric' => 'يجب أن تكون التكلفة رقمًا',
            'cost.min' => 'يجب أن تكون التكلفة موجبة'
        ]);

        // Check validation
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'فشل إضافة الخدمة');
        }

        // Create service
        try {
            Service::create([
                'name' => $request->input('name'),
                'cost' => $request->input('cost')
            ]);

            return redirect()->back()->with('success', 'تمت إضافة الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the service
        $service = Service::findOrFail($id);

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'cost' => 'required|numeric|min:0'
        ], [
            // Custom error messages
            'name.unique' => 'هذه الخدمة موجودة بالفعل',
            'name.required' => 'يجب إدخال اسم الخدمة',
            'cost.required' => 'يجب إدخال تكلفة الخدمة',
            'cost.numeric' => 'يجب أن تكون التكلفة رقمًا',
            'cost.min' => 'يجب أن تكون التكلفة موجبة'
        ]);

        // Check validation
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'فشل تحديث الخدمة');
        }

        // Update service
        try {
            $service->update([
                'name' => $request->input('name'),
                'cost' => $request->input('cost')
            ]);

            return redirect()->back()->with('success', 'تم تحديث الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id)
    {
        try {
            // Find the service
            $service = Service::findOrFail($id);

            // Check if service is used in any existing records
            // Add your specific business logic here, for example:
            // if ($service->bookings()->exists()) {
            //     return redirect()->back()->with('error', 'لا يمكن حذف هذه الخدمة لارتباطها بحجوزات');
            // }

            // Delete the service
            $service->delete();

            return redirect()->back()->with('success', 'تم حذف الخدمة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الخدمة: ' . $e->getMessage());
        }
    }

    /**
     * Optional: Search services
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        $services = Service::where('name', 'LIKE', "%{$query}%")
            ->orWhere('cost', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($services);
    }
}
