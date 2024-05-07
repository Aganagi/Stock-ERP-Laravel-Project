<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Client;
use App\Models\Brand;
use Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->get();
        $products = Product::with('brand')->get();
        $clients = Client::get();
        $brands = Brand::get();

        $totalProfitClosure = function ($order) {
            return $order->amount * ($order->product->sell - $order->product->buy);
        };

        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalOrders = Order::count();
        $totalProfit = Order::with('product')->get()->map($totalProfitClosure)->sum();

        return view('orders', compact('orders', 'clients', 'products', 'brands'),
        [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]
        );
    }

    public function fetchAll()
    {
        $orders = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->get();
        $output = '';
        if ($orders->count() > 0) {
            $output .= '<table class="table table-hover table-sm text-center align-middle">
            <thead>
              <tr>
                <th style="color:#1215E1;"  width="8%">No</th>
                <th style="color:#1215E1;">Client</th>
                <th style="color:#1215E1;">Product</th>
                <th style="color:#1215E1;">Order Quantity</th>
                <th style="color:#1215E1;">Created Date</th>
                <th style="color:#1215E1;">Action</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($orders as $order) {
                $output .= '<tr style="color: #9C27B0;">
                <td class="number"></td>
                <td>' . $order->client->name . ' ' . $order->client->surname . '</td>
                <td>' . $order->product->brand->brand . ' (' . $order->product->product . '-' . $order->product->quantity . ')</td>
                <td>' . $order->amount . '</td>
                <td>' . Carbon::parse($order->created_at)->format('d/m/Y') . '</td>
                <td>';
                if ($order->confirm == 0) {
                    $output .= '<a href="#" id="' . $order->id . '" class="text-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editOrderModal" title="Edit"><i style="color:navy" class="bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $order->id . '" class="text-danger mx-1 deleteIcon" title="Delete"><i style="color:red" class="bi-trash h4"></i></a>

                  <a href="#" id="' . $order->id . '" class="text-success mx-1 confirm" title="Confirm"><i class="bi bi-check-lg"></i></a>';
                }
                if ($order->confirm == 1) {
                    $output .= '<a href="#" id="' . $order->id . '" class="text-warning mx-1 cancel" title="Cancel"><i class="bi bi-x-lg"></i></a>';
                }
                $output .= '</td>
                           </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            return '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'client_id' => 'required',
                'product_id' => 'required',
                'amount' => 'required|numeric',
            ],
            $message = [
                'client_id.required' => 'The client filed is required',
                'product_id.required' => 'The product field is required',
                'amount.required' => 'The quantity filed is required',
                'amount.numeric' => 'The quantity must be a number'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        } else {
            $order = new Order();
            $order->client_id = $request->input('client_id');
            $order->product_id = $request->input('product_id');
            $order->amount = $request->input('amount');
            $order->confirm = 0;
            $order->user_id = auth()->user()->id;
            $order->save();

            return response()->json([
                'status' => 200
            ]);
        }
    }


    public function edit(Request $request)
    {
        $id = $request->id;
        $orders = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->find($id);
        return response()->json($orders);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
            'product_id' => 'required',
            'amount' => 'required|numeric',
        ], 
        $message = [
            'client_id.required' => 'The client filed is required',
            'product_id.required' => 'The product field is required',
            'amount.required' => 'The quantity filed is required',
            'amount.numeric' => 'The quantity must be a number'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {

            $order = Order::find($request->input('order_id'));

            $data = [
                'client_id' => $request->input('client_id', $order->client_id),
                'product_id' => $request->input('product_id', $order->product_id),
                'amount' => $request->input('amount', $order->amount),
            ];

            $order->update($data);

            return response()->json(['status' => 200]);
        }
    }


    public function delete(Request $request)
    {
        $id = $request->id;
        $order = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->find($id);
        $order->delete();
    }

    public function confirmOrder(Request $request)
    {
        $id = $request->id;
        $order = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->find($id);
        $product = Product::find($order->product_id);
        $amount = $order->amount;
        $quantity = $product->quantity;
        if ($amount <= $quantity) {
            $result = $quantity - $amount;
            $product->quantity = $result;
            $product->save();
            $order->confirm = '1';
            $order->save();
        } else {
            return response()->json(['message' => 'There are not enough products to confirm the order'], 422);
        }
        return response()->json(['status' => 200]);
    }

    public function cancelOrder(Request $request)
    {
        $id = $request->id;
        $order = Order::with('client', 'product.brand')->where(['user_id' => auth()->user()->id])->find($id);
        $product = Product::find($order->product_id);
        $amount = $order->amount;
        $quantity = $product->quantity;
        if ($amount <= $quantity) {
            $result = $quantity + $amount;
            $product->quantity = $result;
            $product->save();
            $order->confirm = '0';
            $order->save();
        } else {
            return response()->json(['message' =>'Your order has not been canceled.'], 422);
        }
        return response()->json(['status' => 200]);
    }
}