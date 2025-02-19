<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Order;

class HomeController extends Controller
{
    public function place_order(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        $product = Product::find($request->product_id);

        if ($product->quantity < $request->quantity) {
            return response()->json(['message' => 'out of stock'], 400);
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        // Reduce stock
        $product->decrement('quantity', $request->quantity);

        return response()->json(['message' => 'Order placed successfully', 'order' => $order], 201);
    }

    public function add_product(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required',
        ]);

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath = $request->file('picture')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'picture' => $picturePath,
        ]);

        return response()->json(['message' => 'Product added successfully', 'product' => $product], 201);
    }
}
