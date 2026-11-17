<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::query()
            ->where('company_id', $request->user()->company_id)
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(20);

        return Inertia::render('Orders/Index', [
            'orders' => [
                'data' => $orders->getCollection()->map(fn (Order $order): array => [
                    'id' => $order->id,
                    'created_at' => $order->created_at->toIso8601String(),
                    'status' => $order->status,
                    'total_cents' => (int) $order->total_cents,
                ])->all(),
                'links' => $orders->linkCollection(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }
}
