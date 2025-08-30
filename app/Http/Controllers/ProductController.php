<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    //
    public function index(){
        return view('products.index');
    }
    public function create(){
        return view('products.create');
    }
    public function store(Request $request): RedirectResponse {
        $data = $request->validate([
            // Adjust validation rules for product fields, e.g.:
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $newProduct = Product::create($data);
        return redirect()->route('product.index');
    }
}