<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Service;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        $services = Service::all();
        return view('items-services.index', compact('items', 'services'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        Item::create($validatedData);
        return redirect()->route('items-services.index')->with('success', 'Item added successfully');
    }

    public function update(Request $request, Item $item)
    {
        $validatedData = $request->validate([
            'item' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0'
        ]);

        $item->update($validatedData);
        return redirect()->route('items-services.index')->with('success', 'Item updated successfully');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items-services.index')->with('success', 'Item deleted successfully');
    }
}
