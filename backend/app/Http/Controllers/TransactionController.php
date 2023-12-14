<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::paginate(10);

        return response()->json([
            'status' => 200,
            'message' => 'Transactions retrieved successfully.',
            'data' => $transactions,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|integer|min:1',
        ]);

        $product = Product::find($validatedData['product_id']);
        $price = $product->price;

        $paymentAmount = $validatedData['quantity'] * $price;

        $referenceResponse = Http::withHeaders([
            'X-API-KEY' => 'DATAUTAMA',
            'X-SIGNATURE' => hash('sha256', 'POST:DATAUTAMA'),
        ])->post('http://tes-skill.datautama.com/test-skill/api/v1/transactions', [
            'quantity' => $validatedData['quantity'],
            'price' => $price,
            'payment_amount' => $paymentAmount,
        ]);

        if ($referenceResponse->status() === 200 && $referenceResponse['code'] === '20000') {
            $responseData = $referenceResponse->json()['data'];
            $referenceNo = $responseData['reference_no'];

            $transaction = Transaction::create([
                'reference_no' => $referenceNo,
                'price' => $price,
                'payment_amount' => $paymentAmount,
                'quantity' => $validatedData['quantity'],
                'product_id' => $validatedData['product_id'],
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Transaction added successfully.',
                'data' => $transaction,
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function search($keyword)
    {

        $transactions = Transaction::where('price', 'like', '%' . $keyword . '%')
            ->latest()->paginate(8);

        if (is_null($transactions->first())) {
            return response()->json([
                'status' => 404,
                'message' => 'No transaction found!',
            ], 404);
        }

        $response = [
            'status' => 200,
            'message' => 'Transactions are retrieved successfully.',
            'data' => $transactions,
        ];

        return response()->json($response, 200);
    }
}
