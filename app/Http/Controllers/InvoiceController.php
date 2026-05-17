<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice): View
    {
        $invoice->load('deal.contact');

        $lineItems = collect($invoice->line_items ?? []);

        $grandTotal = $lineItems->sum(
            fn ($item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)
        );

        return view('invoices.show', compact('invoice', 'lineItems', 'grandTotal'));
    }
}
