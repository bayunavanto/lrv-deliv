<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    //
    public function createOrder(Request $request) {
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|integer|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'restaurant_id' => 'required|integer|exists:users,id',
            'shipping_cost' => 'required|integer'
        ]);

        $user = $request->user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $shippingAddress = $user->address;
        $data['shipping_address'] = $shippingAddress;
        $shippingLatLong = $user->latlong;
        $data['shipping_latlong'] = $shippingLatLong;

        $order = Order::create($data);

        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);
            $orderItem = new OrderItem([
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
            $order->items()->save($orderItem);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => $order
        ]);

    }

    public function updatePurchaseStatus(Request $request, $id) {
        $request->validate([
            'status' => 'required|string|in:processing,completed,cancelled'
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order updated successfully',
            'data' => $order
        ]);
    }

    public function orderHistory(Request $request) {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all order history',
            'data' => $orders
        ]);
    }

    public function cancelOrder(Request $request, $id) {
        $order = Order::find($id);
        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }

    public function getOrdersByStatus(Request $request) {
        $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled',
        ]);

        $user = $request->user();
        $orders = Order::where('restaurant_id', $user->id)
            ->where('status', $request->status)
                ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all order by status',
            'data' => $orders
        ]);
    }

    public function updateOrderStatus(Request $request, $id) {
        $request->validate([
            'status' => 'required|string|in:processing,completed,cancelled,ready_for_delivery,prepared'
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    public function getOrderByStatusDriver(Request $request) {
        $request->validate([
            'status' => 'required|string|in:processing,completed,cancelled,ready_for_delivery,prepared'
        ]);

        $user = $request->user();
        $orders = Order::where('driver_id', $user->id)
            ->where('status', $request->status)
                ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all order by status',
            'data' => $orders
        ]);
    }

    public function getOrderStatusReadyForDelivery(Request $request) {
        // $user = $request->user();
        $orders = Order::with('restaurant')
            ->where('status', 'ready_for_delivery')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get All ordres by status ready for delivery',
            'data' => $orders
        ]);
    }

    public function updateOrderStatusDriver(Request $request, $id) {
        $request->validate([
            'status' => 'required|string|in:processing,completed,cancelled,on_the_way,delivered'
        ]);

        $order = Order::find($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }
}
