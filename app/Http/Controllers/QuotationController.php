<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();

        return view('quotations.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quotation_date' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric',
            'discount_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);

        $quotation = Quotation::create($validated);

        foreach ($request->lines as $line) {
            $line['quotation_id'] = $quotation->id;
            QuotationLine::create($line);
        }

        return redirect()->route('quotations.create')->with('success', 'تم حفظ الكوتيشا بنجاح.');
    }
}