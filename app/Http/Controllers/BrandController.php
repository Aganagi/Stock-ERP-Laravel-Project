<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Models\Product;
use App\Models\Client;
use App\Models\Order;

class BrandController extends Controller
{

    // set index page view
    public function index()
    {
        $totalProfitClosure = function ($order) {
            return $order->amount * ($order->product->sell - $order->product->buy);
        };

        $totalProducts = Product::count();
        $totalClients = Client::count();
        $totalOrders = Order::count();
        $totalProfit = Order::with('product')->get()->map($totalProfitClosure)->sum();

        return view('brands', [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]);
    }

    // handle fetch all eamployees ajax request
    public function fetchAll()
    {
        $brands = Brand::where(['user_id' => auth()->user()->id])->get();
        $output = '';
        if ($brands->count() > 0) {
            $output .= '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th style="color:#1215E1;"  width="8%">No</th>
                <th style="color:#1215E1;">Brand</th>
                <th style="color:#1215E1;">Image</th>
				<th style="color:#1215E1;">Created Date/Time</th>
                <th style="color:#1215E1;">Action</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($brands as $brand) {
                $output .= '<tr style="color: #9C27B0;">
                <td class="number" style="color: #9C27B0;"> </td>
                <td style="color: #9C27B0;">' . $brand->brand . '</td>
                <td class="text-center" style="color: #9C27B0;"><img src="storage/images/' . $brand->image . ' " style="widht:50px; height:auto;"></td>
				<td style="color: #9c27b0;">' . Carbon::parse($brand->created_at)->format('d/m/Y H:i') . '</td>
                <td>
                  <a href="#" id="' . $brand->id . '" class="text-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editBrandModal"><i style="color:navy;" class="bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $brand->id . '" class="text-danger mx-1 deleteIcon"><i style="color:red;" class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }
    }

    // handle insert a new Brand ajax request
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'brand' => 'required|unique:brands',
            'image' => 'required|mimes:png,jpg,gif,svg,jpeg|max:1024'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);

            $brandData = [
                'brand' => $request->brand,
                'image' => $fileName,
                'user_id' => auth()->user()->id
            ];

            Brand::create($brandData);

            return response()->json([
                'status' => 200,
            ]);
        }

    }

    // handle edit an Brand ajax request
    public function edit(Request $request)
    {
        $id = $request->id;
        $brand = Brand::where(['user_id' => auth()->user()->id])->find($id);
        return response()->json($brand);
    }

    // handle update an Brand ajax request
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'brand' => 'required|unique:brands',
            'image' => 'image|mimes:jpeg,jpg,png,gif,svg|max:1024'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $fileName = '';
            $brand = Brand::find($request->brand_id);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/images', $fileName);
                if ($brand->image) {
                    Storage::delete('public/images/' . $brand->image);
                }
            } else {
                $fileName = $request->brand_image;
            }
            $data = ['brand' => $request->brand, 'image' => $fileName];

            $brand->update($data);

            return response()->json(['status' => 200,]);
        }
    }

    // handle delete an Brand ajax request
    public function delete(Request $request)
    {
        $id = $request->id;
        $brand = Brand::where(['user_id' => auth()->user()->id])->find($id);
        if (Storage::delete('public/images/' . $brand->image)) {
            Brand::destroy($id);
        }
    }
}
