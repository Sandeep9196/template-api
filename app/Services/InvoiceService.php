<?php

namespace App\Services;
use Illuminate\Http\JsonResponse;
use App\Models\Invoice;
use Illuminate\Http\Request;
use PDF;

class InvoiceService
{

    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Invoice())->newQuery()->orderBy($sortBy, $sortOrder);
            $results = $query->select('invoices.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function pdfInvoicesDownload(Request $request, $orderId)
    {

        $reqId=  Invoice::whereOrderId($orderId)->first();
        if (empty($reqId)) {
            echo "Invalid Invoice Id requested" ;
            exit();
        }

        $invoice = Invoice::whereOrderId($orderId)->with(['customer','orderProduct', 'order'])->first();

        if (!empty($invoice)) {
                $responseData['invoice_no'] = $invoice['invoice_number'] ;
                $responseData['invoice_date'] = $invoice['created_at'] ;
                $responseData['customer_name'] =$invoice->customer->first_name. " ". $invoice->customer->last_name;
                $responseData['customer_phone'] =$invoice->customer->phone_number;
                $responseData['customer_email'] =$invoice->customer->email;
                $responseData['referral'] =$invoice->customer->referral_code;
                $responseData['order_id'] =$invoice->order->order_id;
                $responseData['total_amount'] =$invoice->order->total_amount;
                $responseData['total_products'] =$invoice->order->total_products;
                $responseData['total_slots'] =$invoice->order->total_slots;
                $responseData['total_quantity'] =$invoice->order->total_quantity;
                $responseData['orderProducts']  = $invoice->orderProduct;
                $responseData['amount'] =$invoice->orderProduct[0]->amount;
                $responseData['quantity'] =$invoice->orderProduct[0]->quantity;
                $responseData['product_slug'] = $invoice->orderProduct[0]->product->slug ;

        }
		$pdf = PDF::loadView('pdf.document', compact('responseData'));
		return $pdf->stream('InvoicePDFDownload.pdf');
    }

}
