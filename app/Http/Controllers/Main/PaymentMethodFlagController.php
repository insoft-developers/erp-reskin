<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethodFlags;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentMethodFlagController extends Controller
{
    public function getData(Request $request)
    {
        $userId = $request->session()->get('id');
        $query = PaymentMethodFlags::query();

        // Order by
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');
            $columns = $request->input('columns');
            $orderColumn = $columns[$orderColumnIndex]['data'];

            $allowedColumns = [
                'group',
                'payment_method',
                'flag',
                'created_at'
            ];

            if (in_array($orderColumn, $allowedColumns)) {
                if ($orderColumn == 'date') {
                    $orderColumn = 'created'; // Assuming the column name in the database is 'created'
                }
                $query->orderBy($orderColumn, $orderDirection);
            }
        }

        $query->where('user_id', $userId);
        return DataTables::of($query)
            ->addColumn('group', function ($row) {
                return $row->group;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d H:i:s');
            })
            // ->addColumn('action', function ($row) {
            //     $editUrl = route('payment-method-flag.edit', $row->id);
            //     $deleteUrl = route('payment-method-flag.destroy', $row->id);
            //     return '<div style="display: flex; gap: 10px;">
            //                 <a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a>
            //                 <form action="' . $deleteUrl . '" method="POST" style="display:inline;">
            //                     ' . csrf_field() . '
            //                     ' . method_field('DELETE') . '
            //                     <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
            //                 </form>
            //             </div>';
            // })
            ->addColumn('action', function ($data) {
                return view('main.payment_method_flag.action_datatable', compact('data'));
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $data['view'] = 'apa ajalah';
        return view('main.payment_method_flag.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'flag' => 'required|string|max:10',
        ]);

        // Explode the payment_method value
        $paymentMethodParts = explode('|', $request->input('payment_method'));
        $group = $paymentMethodParts[0];
        $paymentMethod = $paymentMethodParts[1] !== 'null' ? $paymentMethodParts[1] : null;

        // Check if the combination of group and payment_method already exists
        $existingFlag = PaymentMethodFlags::where('group', $group)
            ->where('user_id', $this->get_branch_id($request->session()->get('id')))
            ->where('payment_method', $paymentMethod)
            ->where('flag', $request->input('flag'))
            ->first();

        if ($existingFlag) {
            return redirect()->back()->with('error', 'The combination of group and payment method already exists.');
        }

        PaymentMethodFlags::create([
            'user_id' => $request->session()->get('id'),
            'group' => $group,
            'payment_method' => $paymentMethod,
            'flag' => $request->input('flag'),
        ]);

        return redirect()->route('payment-method-setting.index')->with('success', 'Payment Method Flag created successfully.');
    }

    public function edit($id)
    {
        $paymentMethodFlag = PaymentMethodFlags::findOrFail($id);
        $paymentMethodFlag->payment_method = $paymentMethodFlag->group . '|' . ($paymentMethodFlag->payment_method ?? 'null');
        $data['view'] = '';
        $data['paymentMethodFlag'] = $paymentMethodFlag;
        return view('main.payment_method_flag.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string|max:255',
            'flag' => 'required|string|max:255',
        ]);

        // Explode the payment_method value
        $paymentMethodParts = explode('|', $request->input('payment_method'));
        $group = $paymentMethodParts[0];
        $paymentMethod = $paymentMethodParts[1] !== 'null' ? $paymentMethodParts[1] : null;

        $paymentMethodFlag = PaymentMethodFlags::findOrFail($id);
        $paymentMethodFlag->update([
            'group' => $group,
            'payment_method' => $paymentMethod,
            'flag' => $request->input('flag'),
        ]);

        return redirect()->route('payment-method-setting.index')->with('success', 'Flag Metode Pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $paymentMethodFlag = PaymentMethodFlags::findOrFail($id);
        $paymentMethodFlag->delete();

        return redirect()->route('payment-method-setting.index')->with('success', 'Payment Method Flag has been deleted.');
    }

    // tolong buatkan fungsi getFlags ambil dari model PaymentMethodFlags filter user_id dengan session('id')
    public function getFlags()
    {
        $userId = $this->get_branch_id(session('id'));
        $flags = PaymentMethodFlags::where('user_id', $userId)->get();
        return response()->json($flags);
    }
}
