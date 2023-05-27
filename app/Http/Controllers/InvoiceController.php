<?php

namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function all()
    {
        return response()->json(Invoice::all(), 200);
    }

    public function pdfInvoicesDownload(Request $request, $orderId)
    {
        return $this->invoiceService->pdfInvoicesDownload($request,$orderId);
    }
}
