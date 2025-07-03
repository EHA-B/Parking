<?php
namespace App\Http\Controllers;

use App\Models\BoxTransaction;
use App\Models\Customer;
use App\Models\BoxMonthlyProfit;
use App\Models\BoxClosing;
use App\Models\CurrentBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
    public function index(Request $request)
    {
        $transactions = BoxTransaction::with('customer')->orderByDesc('created_at')->get();
        $customers = Customer::all();
        $totalBox = BoxTransaction::sum(DB::raw("CASE WHEN type = 'income' THEN amount ELSE -amount END"));
        $monthlyProfits = BoxMonthlyProfit::orderByDesc('year')->orderByDesc('month')->get();

        // Determine selected month/year (default: current)
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $closing = \App\Models\BoxClosing::where('month', $selectedMonth)->where('year', $selectedYear)->first();
        $closingBalance = $closing ? $closing->closing_balance : null;

        // Get current box balance
        $currentBox = \App\Models\CurrentBox::first();
        $currentBoxBalance = $currentBox ? $currentBox->current_balance : 0;

        return view('box.index', compact('transactions', 'customers', 'totalBox', 'monthlyProfits', 'closingBalance', 'selectedMonth', 'selectedYear', 'currentBoxBalance'));
    }

    public function storeIncome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
        ]);
        BoxTransaction::create([
            'amount' => $request->amount,
            'type' => 'income',
            'customer_id' => $request->customer_id,
            'notes' => $request->notes,
        ]);
        // Update current box balance
        $box = CurrentBox::first();
        if (!$box) {
            $box = CurrentBox::create(['current_balance' => 0]);
        }
        $box->current_balance += $request->amount;
        $box->save();
        return redirect()->route('box.index');
    }

    public function storeOutcome(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        BoxTransaction::create([
            'amount' => $request->amount,
            'type' => 'outcome',
            'customer_id' => null,
            'notes' => $request->notes,
        ]);
        // Update current box balance
        $box = CurrentBox::first();
        if (!$box) {
            $box = CurrentBox::create(['current_balance' => 0]);
        }
        $box->current_balance -= $request->amount;
        $box->save();
        return redirect()->route('box.index');
    }

    public function monthlyProfits()
    {
        $monthlyProfits = BoxMonthlyProfit::orderByDesc('year')->orderByDesc('month')->get();
        return response()->json($monthlyProfits);
    }

    public function calculateAndStoreCurrentMonthProfit()
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;
        $profit = BoxTransaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum(DB::raw("CASE WHEN type = 'income' THEN amount ELSE -amount END"));

        $existing = BoxMonthlyProfit::where('month', $month)->where('year', $year)->first();
        if ($existing) {
            $existing->update([
                'profit' => $profit,
                'calculated_at' => $now,
            ]);
        } else {
            BoxMonthlyProfit::create([
                'month' => $month,
                'year' => $year,
                'profit' => $profit,
                'calculated_at' => $now,
            ]);
        }

        // Store the closing balance for the month
        $closing_balance = BoxTransaction::sum(DB::raw("CASE WHEN type = 'income' THEN amount ELSE -amount END"));
        $existingClosing = BoxClosing::where('month', $month)->where('year', $year)->first();
        if ($existingClosing) {
            $existingClosing->update([
                'closing_balance' => $closing_balance,
                'closed_at' => $now,
            ]);
        } else {
            BoxClosing::create([
                'month' => $month,
                'year' => $year,
                'closing_balance' => $closing_balance,
                'closed_at' => $now,
            ]);
        }

        // Reset current box balance to 0
        $box = CurrentBox::first();
        if ($box) {
            $box->current_balance = 0;
            $box->save();
        }

        return redirect()->route('box.index')->with('success', 'تم حساب وتخزين ربح الشهر الحالي وتخزين رصيد الصندوق بنجاح! وتم تصفير الصندوق الحالي.');
    }
} 