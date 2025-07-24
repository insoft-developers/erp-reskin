<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\MlAbsensiStaff;
use App\Models\MlAccount;
use Illuminate\Http\Request;

class LaporanKunjunganController extends Controller
{
    public function index(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'report visit';

        return view('main.report.visit.index', compact('view', 'from', 'userKey'));
    }

    /**
     * Display a listing of the resource.
     * view in web
     */
    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->account->fullname ?? null;
            })
            ->addColumn('address', function ($data) {
                return $data->address . "<br> <a target='_blank' href='$data->link_address'>Maps</a>";
            })
            ->addColumn('visited', function ($data) {
                return $data->visited . "<br> <a onclick=\"showPhoto('{$data->photo}')\">Photo</a>";
            })
            ->addColumn('contact', function ($data) {
                $nomor = $data->contact['wa'];
                if (substr($nomor, 0, 1) === '0') {
                    $nomor = substr($nomor, 1);
                }
                $nomor = '62' . $nomor;
                $result = ($data->contact['name'] ?? '') . " - <a href='https://wa.me/" . $nomor . "' target='_blank'>" . $data->contact['wa'] . "</a>";

                return $result;
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at->format('d M Y H:i');
            })
            ->addColumn('is_approved', function ($data) {
                if ($data->is_approved) {
                    return 'Approved';
                } else {
                    return 'Not Approved';
                }
            })
            ->addColumn('action', function ($data) {
                $btn = '<div class="d-flex">';
                if ($data->is_approved == 0) {
                    $btn .= '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm me-2" onclick="editData(' . $data->id . ')">Approve</a>';
                }
                $btn .= '<a href="javascript:void(0)" class="btn btn-success btn-sm me-2" onclick="show(' . $data->id . ')">Catatan</a>';
                $btn .= '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" onclick="deleteData(event, ' . $data->id . ')">Hapus</a>';
                $btn .= '</div>';

                return $btn;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $input = $request->all();

        // filter
        $isToday = $input['isToday'] ?? false;
        $isYesterday = $input['isYesterday'] ?? false;
        $isLastMonth = $input['isLastMonth'] ?? false;
        $isThisYear = $input['isThisYear'] ?? false;
        $isLastYear = $input['isLastYear'] ?? false;

        $startDate = now()->startOfMonth()->format('Y-m-d 00:00:00');
        $endDate = now()->endOfMonth()->format('Y-m-d 23:59:59');

        if ($isToday) {
            $startDate = now()->format('Y-m-d 00:00:00');
            $endDate = now()->format('Y-m-d 23:59:59');
        }
        if ($isYesterday) {
            $startDate = now()->subDay()->format('Y-m-d 00:00:00');
            $endDate = now()->subDay()->format('Y-m-d 23:59:59');
        }
        if ($isLastMonth) {
            $startDate = now()->subMonth()->startOfMonth()->format('Y-m-d 00:00:00');
            $endDate = now()->subMonth()->endOfMonth()->format('Y-m-d 23:59:59');
        }
        if ($isThisYear) {
            $startDate = now()->format('Y-01-01 00:00:00');
            $endDate = now()->format('Y-12-31 23:59:59');
        }
        if ($isLastYear) {
            $startDate = now()->subYear()->format('Y-01-01 00:00:00', '-1 years');
            $endDate = now()->subYear()->format('Y-12-31 23:59:59', '-1 years');
        }

        $startDate = $input['startDate'] ?? $startDate;
        $endDate = $input['endDate'] ?? $endDate;

        $columns = [
            'id',
            'account_id',
            'address',
            'visited',
            'created_at',
            'is_approved'
        ];
        $keyword = $request->keyword;
        $user = MlAccount::where('id', session('id'))->first();
        $user_id = MlAccount::where('branch_id', $user->branch_id)->pluck('id');

        $query = MlAbsensiStaff::where(function ($query) use ($keyword, $columns, $request) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
                if ($request->isApproved != null || $request->isApproved != '') {
                    $query->where('is_approved', $request->isApproved);
                }
            })
            ->whereIn('account_id', $user_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc')
            ->get();
            
        return $query;
    }

    /**
     * Display a listing of the resource.
     * view in mobile
     */

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Update status from not approved to approved in web
     */
    public function update(Request $request, string $id)
    {
        try {
            $input = $request->all();
            $data = [
                "is_approved" => $input['is_approved'],
            ];
            return $this->atomic(function () use ($data, $id) {
                $update = MlAbsensiStaff::findOrFail($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Update',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Update',
                'error_message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * delete in web
     */
    public function destroy(string $id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $delete = MlAbsensiStaff::findOrFail($id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Hapus',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Hapus',
                'error_message' => $th->getMessage(),
            ]);
        }
    }

    public function resistance_index()
    {
        $view = 'resistance';

        $sales = MlMarketing::select('id', 'name', 'referal_source')->get();

        return view('main.resistance.index', compact('view', 'sales'));
    }

    /**
     * Display a listing of the resource.
     * view in web
     */
    public function resistance_data(Request $request)
    {
        $data = $this->getResistanceData($request);

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('name', function ($data) {
                return $data->marketing->name;
            })
            ->addColumn('address', function ($data) {
                return $data->address . "<br> <a href='$data->link_address'>Maps</a>";
            })
            ->addColumn('contact', function ($data) {
                return $data->contact['name'] . ' | ' . $data->contact['wa'];
            })
            ->addColumn('resistance_category', function ($data) {
                return $data->resistance['category'];
            })
            ->addColumn('resistance_description', function ($data) {
                return $data->resistance['description'];
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getResistanceData(Request $request)
    {
        $columns = [
            'id',
            'account_id',
            'address',
            'link_address',
            'contact',
            'resistance',
        ];
        $keyword = $request->keyword;

        $query = MlAbsensiStaff::select($columns)
            ->where('resistance', '!=', null)
            ->where(function ($query) use ($keyword, $columns, $request) {
                if ($keyword != '') {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
                if ($request->marketingId) {
                    $query->where('account_id', $request->marketingId);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
        return $query;
    }

    /**
     * Update the specified resource in storage.
     * Update resistance data in mobile
     */
    public function update_resistance(Request $request, string $id)
    {
        $input = $request->all();
        $rules = [
            "title" => ['required'],
            "category" => ['required', 'in:harga,kompetitor,fitur,lain-lain'],
            "description" => ['required', 'min:50'],
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'reason' => $validator->errors()
            ], 400);
        }

        try {
            $data = [
                "resistance" => [
                    "title" => $input['title'],
                    "category" => $input['category'],
                    "description" => $input['description']
                ]
            ];

            return $this->atomic(function () use ($data, $id) {
                $update = MlAbsensiStaff::findOrFail($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Update',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Update',
                'error_message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * delete resistance data in mobile
     */
    public function destroy_resistance(string $id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $delete = MlAbsensiStaff::findOrFail($id)->update([
                    "resistance" => null
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Hapus',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Hapus',
                'error_message' => $th->getMessage(),
            ]);
        }
    }

    public function show_photo(Request $request)
    {
        $input = $request->all();
        $photo = $input['photo'];
        $view = 'show-photo';

        return view('main.report.visit.show_photo', compact('view', 'photo'));
    }

    public function show($id)
    {
        $view = 'show';
        $data = MlAbsensiStaff::findOrFail($id);

        return view('main.report.visit.detail', compact('view', 'data'));
    }
}
