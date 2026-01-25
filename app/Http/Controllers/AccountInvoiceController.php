<?php

namespace App\Http\Controllers;

use App\Models\AccountInvoice;
use Illuminate\Http\Request;

class AccountInvoiceController extends Controller
{
  /**
     * 🔹 Get ALL invoices (for /invoices page)
     * GET /invoices
     */
    public function all()
    {
        $invoices = AccountInvoice::orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $invoices
        ], 200);
    }

    /**
     * 🔹 Upload one or multiple invoices
     * POST /invoices
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_id'        => 'required|exists:lease_expenses,expense_id',
            'invoice_number'   => 'nullable|string',
            'invoice_date'     => 'nullable|date',
            'amount'           => 'nullable|string',
            'files'            => 'required',
            'files.*'          => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $uploaded = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {

                $filename = time() . '_' . $file->getClientOriginalName();

                // Store in: storage/app/public/invoices
                $path = $file->storeAs('invoices', $filename, 'public');

                $invoice = AccountInvoice::create([
                    'expense_id'      => $request->expense_id,
                    'invoice_number' => $request->invoice_number,
                    'invoice_date'   => $request->invoice_date,
                    'amount'         => $request->amount,
                    'file_name'      => $file->getClientOriginalName(),
                    'file_path'      => $path, // only relative path
                    'uploaded_by'    => $request->user()->user_id ?? null,
                ]);

                $uploaded[] = $invoice;
            }
        }

        return response()->json([
            'message' => 'Invoices uploaded successfully',
            'data'    => $uploaded
        ], 201);
    }

    /**
     * 🔹 Get invoices by Expense
     * GET /expenses/{expense_id}/invoices
     */
    public function byExpense($expense_id)
    {
        $invoices = AccountInvoice::where('expense_id', $expense_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'expense_id' => $expense_id,
            'data' => $invoices
        ], 200);
    }

    /**
     * 🔹 Show single invoice
     * GET /invoices/{id}
     */
    public function show($id)
    {
        $invoice = AccountInvoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        return response()->json([
            'data' => $invoice
        ], 200);
    }

    /**
     * 🔹 Update invoice metadata (no file replace yet)
     * PUT /invoices/{id}
     */
    public function update(Request $request, $id)
    {
        $invoice = AccountInvoice::find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $request->validate([
            'invoice_number' => 'nullable|string',
            'invoice_date'   => 'nullable|date',
            'amount'         => 'nullable|string',
        ]);

        if ($request->has('invoice_number')) {
            $invoice->invoice_number = $request->invoice_number;
        }

        if ($request->has('invoice_date')) {
            $invoice->invoice_date = $request->invoice_date;
        }

        if ($request->has('amount')) {
            $invoice->amount = $request->amount;
        }

        $invoice->save();

        return response()->json([
            'message' => 'Invoice updated successfully',
            'data' => $invoice
        ], 200);
    }

}