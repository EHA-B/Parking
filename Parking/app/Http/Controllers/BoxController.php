<?php
namespace App\Http\Controllers;

use App\Models\BoxTransaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoxController extends Controller
{
    public function index()
    {
        $transactions = BoxTransaction::with('customer')->orderByDesc('created_at')->get();
        $customers = Customer::all();
        $totalBox = BoxTransaction::sum(DB::raw("CASE WHEN type = 'income' THEN amount ELSE -amount END"));
        return view('box.index', compact('transactions', 'customers', 'totalBox'));
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
        return redirect()->route('box.index');
    }
} 