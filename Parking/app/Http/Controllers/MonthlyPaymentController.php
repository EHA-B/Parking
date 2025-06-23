<?php

namespace App\Http\Controllers;

use App\Models\MonthlyPayment;
use App\Models\ParkingSlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyPaymentController extends Controller
{
    public function store(Request $request, ParkingSlot $parkingSlot)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        // Check if this is a monthly parking slot
        if ($parkingSlot->parking_type !== 'monthly') {
            return response()->json([
                'success' => false,
                'message' => 'Payments are only available for monthly parking slots'
            ], 400);
        }

        // Calculate remaining amount
        $totalAmount = $parkingSlot->price;
        $paidAmount = $parkingSlot->getTotalPaidAmount();
        $remainingAmount = max(0, $totalAmount - $paidAmount);

        // Validate payment amount
        if ($request->amount > $remainingAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed remaining amount'
            ], 400);
        }

        // Create the payment record
        $payment = MonthlyPayment::create([
            'parking_slot_id' => $parkingSlot->id,
            'amount' => $request->amount,
            'remaining_amount' => $remainingAmount - $request->amount,
            'payment_status' => ($request->amount == $remainingAmount) ? 'completed' : 'partial',
            'payment_date' => Carbon::now(),
            'payment_method' => $request->payment_method,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'payment' => $payment,
            'remaining_amount' => $remainingAmount - $request->amount
        ]);
    }

    public function getPaymentHistory(ParkingSlot $parkingSlot)
    {
        $payments = $parkingSlot->monthlyPayments()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAmount = $parkingSlot->price;
        $paidAmount = $parkingSlot->getTotalPaidAmount();
        $remainingAmount = $parkingSlot->getRemainingAmount();

        return response()->json([
            'payments' => $payments,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount
        ]);
    }
} 