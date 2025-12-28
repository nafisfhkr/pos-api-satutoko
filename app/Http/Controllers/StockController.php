<?php

namespace App\Http\Controllers;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function index()
    {
        
        $stocks = Stock::with(['product', 'outlet'])->get();

        
        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Stok',
            'data'    => $stocks
        ], 200);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id', // Harus ada di tabel products
            'outlet_id'  => 'required|exists:outlets,id',  // Harus ada di tabel outlets
            'quantity'   => 'required|integer',            // Harus angka bulat
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // 2. Simpan Data ke Database
        $stock = Stock::create([
            'product_id' => $request->product_id,
            'outlet_id'  => $request->outlet_id,
            'quantity'   => $request->quantity,
        ]);

        // 3. Kembalikan Response Sukses
        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil ditambahkan',
            'data'    => $stock
        ], 201);
    }
}
