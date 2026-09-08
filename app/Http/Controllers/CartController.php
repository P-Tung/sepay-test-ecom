<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (! empty($cart)) {
            $products = Product::with('category')
                ->whereIn('id', array_keys($cart))
                ->get()
                ->keyBy('id');
            $updated = false;

            foreach ($cart as $id => &$item) {
                if (isset($products[$id])) {
                    $categoryName = $products[$id]->category?->name ?? 'Chưa phân loại';
                    if (($item['category'] ?? null) !== $categoryName) {
                        $item['category'] = $categoryName;
                        $updated = true;
                    }
                }
            }
            unset($item);

            if ($updated) {
                session()->put('cart', $cart);
            }
        }

        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if ($product->quantity < 1) {
            return back()->with('error', 'Sản phẩm hiện đã hết hàng.');
        }

        $cart = session('cart', []);
        $id = (string) $product->id;
        $quantity = ($cart[$id]['quantity'] ?? 0) + 1;

        if ($quantity > $product->quantity) {
            return back()->with('error', 'Số lượng trong giỏ vượt quá tồn kho.');
        }

        $cart[$id] = [
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => (float) $product->price,
            'category' => $product->category?->name ?? 'Chưa phân loại',
            'image' => $product->image,
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        $cart = session('cart', []);
        $id = (string) $product->id;

        if (! isset($cart[$id])) {
            return redirect()->route('cart.index')->with('error', 'Sản phẩm không có trong giỏ hàng.');
        }

        if ($data['quantity'] > $product->quantity) {
            return redirect()->route('cart.index')->with('error', 'Số lượng vượt quá tồn kho.');
        }

        $cart[$id]['quantity'] = $data['quantity'];
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[(string) $product->id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
