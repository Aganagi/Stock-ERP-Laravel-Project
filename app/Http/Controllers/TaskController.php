<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Task;
use App\Models\Product;
use App\Models\Client;
Use App\Models\Order;
use Carbon\Carbon;

class TaskController extends Controller
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

        return view('tasks', [
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'totalOrders' => $totalOrders,
            'totalProfit' => $totalProfit,
        ]);
    }

    public function fetchAll()
    {
        $tasks = Task::where(['user_id' => auth()->user()->id])->get();
        $output = '';
        if ($tasks->count() > 0) {
            $output .= '<table class="table table-hover table-sm text-center align-middle">
            <thead>
              <tr>
                <th style="color:#1215E1;" width="8%">No</th>
                <th style="color:#1215E1;">Task</th>
                <th style="color:#1215E1;">Created Date</th>
                <th style="color:#1215E1;">End Date</th>
                <th style="color:#1215E1;">The rest of the time</th>
                <th style="color:#1215E1;">Action</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($tasks as $task) {
                $start = Carbon::now();
                $end = Carbon::parse($task->date . ' ' . $task->time);

                $diff = $end->diff($start);

                if ($diff->days > 0) {
                    $result = $diff->format('%d days %h hours %i minutes %s seconds');
                } else {
                    $result = $diff->format('%h hours %i minutes %s seconds');
                }
                $output .= '<tr tyle="color: #9C27B0;">
                <td class="number" style="color: #9C27B0;"></td>
                <td style="color: #9C27B0;">' . $task->task . '</td>
                <td style="color: #9C27B0;">' . Carbon::parse($task->created_at)->format('d-m-Y / h:i') . '</td>
                <td style="color: #9C27B0;">' . Carbon::parse($task->date)->format('d-m-Y') . ' / ' . Carbon::parse($task->time)->format('h:i') . '</td>
                <td style="color: #9C27B0;"><span class="countdown" data-end="' . $end->format('Y-m-d H:i:s') . '"></span></td>
                <td>
                  <a href="#" id="' . $task->id . '" class="text-primary mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editTaskModal"><i style="color:navy;" class="bi bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $task->id . '" class="text-danger mx-1 deleteIcon"><i style="color:red;" class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h4 class="text-center text-secondary my-5">No record present in the database!</h4>';
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:30',
            'date' => 'required',
            'time' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $data = [
                'task' => $request->task,
                'date' => $request->date,
                'time' => $request->time,
                'user_id' =>auth()->user()->id
            ];

            Task::create($data);

            return response()->json([
                'status' => 200
            ]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $task = Task::where(['user_id' => auth()->user()->id])->find($id);
        return response()->json($task);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:30',
            'date' => 'required',
            'time' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } else {
            $task = Task::find($request->task_id);
            $data = [
                'task' => $request->task,
                'date' => $request->date,
                'time' => $request->time
            ];

            $task->update($data);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $task = Task::where(['user_id' => auth()->user()->id])->find($id)->delete();
        return response()->json($task);
    }
}