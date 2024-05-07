<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Order;
use App\Models\Client;
use Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('brand')->where(['user_id' => auth()->user()->id])->get();
        $brands = Brand::get();

        $totalProfitClosure = function ($order) {
            return $order->amount * ($order->product->sell - $order->product->buy);
        };

        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalOrders = Order::count();
        $totalProfit = Order::with('product')->get()->map($totalProfitClosure)->sum();

        return view('products', compact('brands', 'products'),
        [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]);
    }

    public function fetchAll()
    {
        $products = Product::with('brand')->where(['user_id' => auth()->user()->id])->get();
        $output = '';
        if ($products->count() > 0) {
            $output = '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th style="color:#1215E1;"  width="8%">No</th>
                <th style="color:#1215E1;">Brand</th>
                <th style="color:#1215E1;">Product</th>
                <th style="color:#1215E1;">Purchase price</th>
                <th style="color:#1215E1;">Sell price</th>
                <th style="color:#1215E1;">Quantity</th>
                <th style="color:#1215E1;">Image</th>
                <th style="color:#1215E1;">Created Date</th>
                <th style="color:#1215E1;">Action</th>
              </tr>
            </thead>
            <tbody>';

            foreach ($products as $product) {
                $output .= '<tr>
                <td  style="color: #9C27B0;"></td>
                <td  style="color: #9C27B0;">' . $product->brand->brand . '</td>
                <td  style="color: #9C27B0;">' . $product->product . '</td>
                <td  style="color: #9C27B0;">' . $product->buy . '</td>
                <td  style="color: #9C27B0;">' . $product->sell . '</td>
                <td  style="color: #9C27B0;">' . $product->quantity . '</td>
                <td  style="color: #9C27B0;"><img src="storage/images/' . $product->image . '" style="width: 50px; height:50px;"></td>
                <td  style="color: #9C27B0;"> ' . Carbon::parse($product->created_at)->format('d/m/Y') . '</td>
                <td>
                  <a href="#" id="' . $product->id . '" class="text-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editProductModal"><i style="color:navy;" class="bi-pencil-square h4"></i></a>
                  <a href="#" id="' . $product->id . '" class="text-danger mx-1 deleteIcon"><i style="color:red;" class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output = '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }

        return $output;
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'brand_id' => 'required',
            'product' => 'required|string|max:25',
            'buy' => 'required|numeric',
            'sell' => 'required|numeric',
            'quantity' => 'required|numeric',
            'image' => 'required|image|max:2048'
        ], $message = [
                'brand_id.required' => 'The brand field is required',
                'buy.required' => 'The purchase price filed is required',
                'buy.numeric'=>'The purchase price must be a number',
                'sell.reuired' => 'The sell price filed is required',
                'sell.numeric'=>'The sell price must be a number'
            ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);

            $productData = [
                'brand_id' => $request->brand_id,
                'product' => $request->product,
                'buy' => $request->buy,
                'sell' => $request->sell,
                'quantity' => $request->quantity,
                'image' => $fileName,
                'user_id'=>auth()->user()->id
            ];
            Product::create($productData);
            return response()->json([
                'status' => 200,
            ]);
        }

    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $product = Product::with('brand')->where(['user_id' => auth()->user()->id])->find($id);
        return response()->json($product);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required',
            'product' => 'required|string|max:25',
            'buy' => 'required|numeric',
            'sell' => 'required|numeric',
            'quantity' => 'required|numeric',
            'image' => 'image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $fileName = '';
            $product = Product::find($request->product_id);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/images', $fileName);
                if ($product->image) {
                    Storage::delete('public/images/' . $product->image);
                }
            } else {
                $fileName = $request->product_image;
            }
            $productData = [
                'brand_id' => $request->input('brand_id', $product->brand_id),
                'product' => $request->input('product', $product->product),
                'buy' => $request->input('buy', $product->buy),
                'sell' => $request->input('sell', $product->sell),
                'quantity' => $request->input('quantity', $product->quantity),
                'image' => $fileName
            ];
            $product->update($productData);

            return response()->json(['status' => 200]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $product = Product::with('brand')->where(['user_id' => auth()->user()->id])->findOrFail($id);

        $orders = Order::where('product_id', $product->id)->get();

        if ($orders->count() > 0) {
            foreach ($orders as $order) {
                if ($order->confirm == true) {
                    return response()->json(['status' => 422], 422);
                }
            }
        }else{
            if ($product->image) {
                Storage::move('public/images/' . $product->image, 'public/images/deleted/' . $product->image);
            }
    
            $product->delete();
    
            return response()->json(['status' => 200]);
        }
    }
}