<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Product;

class FrontController extends Controller
{

    public function index()
    {
        // membuat query untuk mengambil data produk yang diurutkan berdasarkan tanggal terbaru
        // dan diload 10 data per pagenya
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        // load view index.blade.php dan passing data dari variabel products
        return view('ecommerce.index', compact('products'));
    }
    
}


