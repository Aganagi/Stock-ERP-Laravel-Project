<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Client;
Use App\Models\Order;
use Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function index()
    {
        $totalProfitClosure = function ($order) {
            return $order->amount * ($order->product->sell - $order->product->buy);
        };

        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalOrders = Order::count();
        $totalProfit = Order::with('product')->get()->map($totalProfitClosure)->sum();

        return view('clients', [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]);
    }
    public function fetchAll()
    {
        $clients = Client::where(['user_id' => auth()->user()->id])->get();
        if ($clients->count() > 0) {
            $output = '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th style="color:#1215E1;"  width="8%">No</th>
                <th style="color:#1215E1;">Client Name</th>
                <th style="color:#1215E1;">E-mail</th>
                <th style="color:#1215E1;">Company</th>
                <th style="color:#1215E1;">Phone Number</th>
                <th style="color:#1215E1;">Image</th>
                <th style="color:#1215E1;">Created Date</th>
                <th style="color:#1215E1;">Action</th>
              </tr>
            </thead>
            <tbody>';

            foreach ($clients as $client) {
                $output .= '<tr tyle="color: #9C27B0;">
                <td class="number" style="color: #9C27B0;"></td>
                <td style="color: #9C27B0;">' . $client->name . ' ' . $client->surname . '</td>
                <td style="color: #9C27B0;">' . $client->email . '</td>
                <td style="color: #9C27B0;">' . $client->company . '</td>
                <td style="color: #9C27B0;">' . $client->phone . '</td>
                <td style="color: #9C27B0;"><img src="storage/images/' . $client->image . '" style="widht:50px; height:auto;"></td>
                <td style="color: #9C27B0;"> ' . Carbon::parse($client->created_at)->format('Y-m-d (h:i)') . '</td>
                <td>
                  <a href="#" id="' . $client->id . '" class="text-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editClientModal"><i style="color:navy;" class="bi-pencil-square h4"></i></a>
                  <a href="#" id="' . $client->id . '" class="text-danger mx-1 deleteIcon"><i style="color:red;" class="bi-trash h4"></i></a>
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
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email|unique:clients',
            'company' => 'required',
            'phone' => 'required|regex:/^\+[0-9]{10,15}$/',
            'image' => 'required|mimes:jpg,png,gif,svg,jpeg|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);

            $data = [
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'company' => $request->company,
                'phone' => $request->phone,
                'image' => $fileName,
                'user_id'=>auth()->user()->id
            ];
            Client::create($data);
            return response()->json([
                'status' => 200,
            ]);
        }

    }
    public function edit(Request $request)
    {
        $id = $request->id;
        $client = Client::where(['user_id' => auth()->user()->id])->find($id);
        return response()->json($client);
    }
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
            'company' => 'required',
            'phone' => 'required|regex:/^\+[0-9]{10,15}$/',
            'image' => 'mimes:jpg,png,gif,svg,jpeg|max:2048'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $fileName = '';
            $client = Client::find($request->client_id);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/images', $fileName);
                if ($client->image) {
                    Storage::delete('public/images/' . $client->image);
                }
            } else {
                $fileName = $request->client_image;
            }
            $data = [
                'name' => $request->input('name', $client->name),
                'surname' => $request->input('surname', $client->surname),
                'email' => $request->input('email', $client->email),
                'company' => $request->input('company', $client->company),
                'phone' => $request->input('phone', $client->phone),
                'image' => $fileName
            ];

            $client->update($data);

            return response()->json(['status' => 200,]);
        }
    }
    
    public function delete(Request $request)
    {
        $id = $request->id;
        $client = Client::where(['user_id' => auth()->user()->id])->findOrFail($id);

        $orders = Order::where('client_id', $client->id)->get();

        if ($orders->count() > 0) {
            foreach ($orders as $order) {
                if ($order->confirm == true) {
                    return response()->json(['status' => 422], 422);
                }
            }
        }else{
            if ($client->image) {
                Storage::move('public/images/' . $client->image, 'public/images/deleted/' . $client->image);
            }
    
            $client->delete();
    
            return response()->json(['status' => 200]);
        }
    }
}