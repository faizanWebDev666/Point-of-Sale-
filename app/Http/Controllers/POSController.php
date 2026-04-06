<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\POSTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->limit(24)->get();
        $categories = Product::select('category')->distinct()->pluck('category');
        $transactions = POSTransaction::orderBy('created_at', 'desc')->limit(10)->get();
        return view('pos', compact('products', 'categories', 'transactions'));
    }

    public function saveSale(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array',
                'subtotal' => 'required|numeric',
                'discount_amount' => 'required|numeric',
                'tax_amount' => 'required|numeric',
                'total_amount' => 'required|numeric',
                'payment_method' => 'required|string',
                'amount_paid' => 'required|numeric',
                'customer_name' => 'nullable|string',
                'customer_phone' => 'nullable|string',
                'customer_email' => 'nullable|email',
                'notes' => 'nullable|string',
            ]);

            $transactionId = 'TRX-' . date('YmdHis') . '-' . Str::random(6);
            
            $transaction = POSTransaction::create([
                'transaction_id' => $transactionId,
                'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount_amount'],
                'tax_amount' => $validated['tax_amount'],
                'total_amount' => $validated['total_amount'],
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $validated['amount_paid'],
                'change_amount' => $validated['amount_paid'] - $validated['total_amount'],
                'notes' => $validated['notes'] ?? null,
                'items' => $validated['items'],
                'loyalty_points_earned' => (int)($validated['total_amount'] * 10), // 1 point per 10 cents
            ]);

            foreach ($validated['items'] as $item) {
                Product::where('id', $item['id'])->decrement('stock', $item['qty']);
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Sale completed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getTransactionHistory(Request $request)
    {
        $transactions = POSTransaction::orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 20));
        
        return response()->json($transactions);
    }

    public function getTransaction($id)
    {
        $transaction = POSTransaction::findOrFail($id);
        return response()->json($transaction);
    }

    public function refundTransaction(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'refund_reason' => 'required|string',
                'refund_type' => 'required|in:full,partial',
                'partial_amount' => 'nullable|numeric',
            ]);

            $transaction = POSTransaction::findOrFail($id);

            if ($transaction->status === 'refunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already refunded',
                ], 422);
            }

            $refundAmount = $validated['refund_type'] === 'full' 
                ? $transaction->amount_paid 
                : $validated['partial_amount'];

            // Restore stock
            foreach ($transaction->items as $item) {
                Product::where('id', $item['id'])->increment('stock', $item['qty']);
            }

            $transaction->update([
                'status' => $validated['refund_type'] === 'full' ? 'refunded' : 'partial_refund',
                'refunded_at' => now(),
                'refund_reason' => $validated['refund_reason'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'refund_amount' => $refundAmount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function voidTransaction(Request $request, $id)
    {
        try {
            $transaction = POSTransaction::findOrFail($id);

            if ($transaction->status === 'voided') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction already voided',
                ], 422);
            }

            // Restore stock
            foreach ($transaction->items as $item) {
                Product::where('id', $item['id'])->increment('stock', $item['qty']);
            }

            $transaction->update(['status' => 'voided']);

            return response()->json([
                'success' => true,
                'message' => 'Transaction voided successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getDailySummary()
    {
        $today = now()->startOfDay();
        
        $summary = [
            'total_sales' => POSTransaction::where('status', '!=', 'voided')
                ->where('created_at', '>=', $today)
                ->sum('total_amount'),
            'transaction_count' => POSTransaction::where('status', '!=', 'voided')
                ->where('created_at', '>=', $today)
                ->count(),
            'total_discount' => POSTransaction::where('created_at', '>=', $today)
                ->sum('discount_amount'),
            'total_refunds' => POSTransaction::where('status', 'like', '%refund%')
                ->where('created_at', '>=', $today)
                ->sum('amount_paid'),
            'payment_breakdown' => POSTransaction::where('status', '!=', 'voided')
                ->where('created_at', '>=', $today)
                ->groupBy('payment_method')
                ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
                ->get(),
        ];

        return response()->json($summary);
    }

    public function getStockAlerts()
    {
        $lowStock = Product::where('stock', '<=', 'min_stock')
            ->orWhere('stock', '<=', 5)
            ->get();

        $outOfStock = Product::where('stock', 0)->get();

        return response()->json([
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category', 'all');
        
        $products = Product::query();

        if ($query) {
            $products->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('sku', 'like', "%$query%");
            });
        }

        if ($category !== 'all') {
            $products->where('category', $category);
        }

        return response()->json($products->limit(50)->get());
    }
}
