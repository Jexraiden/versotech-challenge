<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SessionCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private readonly SessionCart $cart) {}

    public function show(Request $request): Response
    {
        return Inertia::render('Cart/Index', [
            'cart' => $this->cart->present($request, $request->user()),
            'companyName' => $request->user()->company->trade_name,
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $input = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100000']]);
        $this->cart->add($request, $request->user(), $product, (int) $input['quantity']);
        return back()->with('cart_success', $product->name.' adicionado ao carrinho.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $input = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100000']]);
        $this->cart->update($request, $request->user(), $product, (int) $input['quantity']);
        return back()->with('cart_success', 'Quantidade atualizada.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $this->cart->remove($request, $product);
        return back()->with('cart_success', 'Item removido do carrinho.');
    }
}
