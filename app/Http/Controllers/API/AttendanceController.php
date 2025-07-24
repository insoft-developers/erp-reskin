<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BusinessGroup;
use App\Models\MlAbsensi;
use App\Models\MlAbsensiStaff;
use App\Models\MlAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function list(Request $request)
    {
        $columns = [
            'id',
            'user_id',
            'clock_in',
            'attachment_clock_in',
            'location_clock_in',
            'clock_out',
            'attachment_clock_out',
            'location_clock_out',
            'start_rest',
            'end_rest',
            'attachment_start_rest',
            'location_start_rest',
            'attachment_end_rest',
            'location_end_rest',
            'start_overtime',
            'end_overtime',
            'location_start_overtime',
            'attachment_start_overtime',
            'location_end_overtime',
            'attachment_end_overtime',
            'created_at',
        ];

        $keyword = $request->search;
        $date = $request->date ?? now()->format('Y-m-d');
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;

        $limit = limitList($per_page);
        $userId = Auth::user()->id ?? session('id');
        $ownerId = $this->get_owner_id($userId);

        $data = Attendance::orderBy('id', 'desc')
            ->where('user_id', $userId)
            ->select($columns)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date)->orWhere('clock_in', $date)->orWhere('clock_out', $date);
            })
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        $bussiness_group = BusinessGroup::where('user_id', $ownerId)->first();
        $account = MlAccount::where('id', $userId)->first();
        
        foreach ($data as $key => $value) {
            $value['nama_toko'] = $bussiness_group->branch_name;
            $value['nama_staff'] = $account->fullname;

            $clock_in_account = Carbon::parse($account->clock_in) ?? '';
            $clock_out_account = Carbon::parse($account->clock_out) ?? '';
            $holiday = isset($value->user->holiday) && ($value->user->holiday != "null") && ($value->user->holiday != '') ? implode(', ', json_decode($account->holiday)) : '-';
            
            $value['jam_kerja'] = (($clock_in_account != '') ? $clock_in_account->format('H:i') : ''). ' - ' . (($clock_out_account != '') ? $clock_out_account->format('H:i') : '');
            $value['hari_libur'] = $holiday;
            $value['mulai_kerja'] = Carbon::parse($account->created_at)->format('Y-m-d');
            $value['attachment_clock_in'] = ($value->attachment_clock_in != null) ? asset('storage/'. $value->attachment_clock_in) : null;
            $value['attachment_clock_out'] = ($value->attachment_clock_out != null) ? asset('storage/'. $value->attachment_clock_out) : null;
            $value['attachment_start_rest'] = ($value->attachment_start_rest != null) ? asset('storage/'. $value->attachment_start_rest) : null;
            $value['attachment_end_rest'] = ($value->attachment_end_rest != null) ? asset('storage/'. $value->attachment_end_rest) : null;
            $value['attachment_start_overtime'] = ($value->attachment_start_overtime != null) ? asset('storage/'. $value->attachment_start_overtime) : null;
            $value['attachment_end_overtime'] = ($value->attachment_end_overtime != null) ? asset('storage/'. $value->attachment_end_overtime) : null;


            $value['clock_in'] = ($value->clock_in != null) ? Carbon::parse($value->clock_in)->format('H:i') : null;
            $value['clock_out'] = ($value->clock_out != null) ? Carbon::parse($value->clock_out)->format('H:i') : null;

            $range_clock_in = Carbon::parse($value->clock_in)->diffInMinutes($clock_in_account, false);
            $range_clock_out = ($value->clock_out != null) ? Carbon::parse($value->clock_out)->diffInMinutes($clock_out_account, false) : 0;

            $value['range_clock_in'] = $range_clock_in;
            $value['range_clock_out'] = ($value->clock_out != null) ? $range_clock_out : 0;

            $value['note_clock_in'] = ($range_clock_in > 0) ? 'Normal '.$range_clock_in.' menit' : 'Telat '.str_replace('-', '', $range_clock_in).' menit';
            $value['note_clock_out'] = ($value->clock_out != null) ? (($range_clock_out > 0) ? 'Awal '.$range_clock_out.' menit' : 'Normal '.str_replace('-', '', $range_clock_out).' menit') : '';

            // NOTE ISTIRAHAT
            $startRest = Carbon::parse($value->start_rest);
            $endRest = Carbon::parse($value->end_rest);

            $restDuration = $startRest->diffInMinutes($endRest);
            $value['note_rest'] = $restDuration > $account->time_break ? 'Telat ' . $restDuration . ' menit' : 'Normal ' . $restDuration . ' menit';

            // NOTE LEMBUR
            $startovertime = Carbon::parse($value->start_overtime);
            $endovertime = Carbon::parse($value->end_overtime);

            $overtimeDuration = $startovertime->diffInMinutes($endovertime);
            $value['note_overtime'] = "Lembur $overtimeDuration menit";
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        try {
            $createData = $this->validateAttendance($request);
            return $this->atomic(function () use ($createData) {
                if ($createData['id'] == null) {
                    $create = Attendance::create($createData);
                }else{
                    $create = Attendance::where('id', $createData['id'])->update($createData);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan',
            ]);
        }
    }

    public function validateAttendance($request)
    {
        $userId = Auth::user()->id ?? session('id');
        $attendance = Attendance::where('user_id', $userId)->whereDate('created_at', now())->orderBy('id', 'desc')->first();
        
        $createData = [];
        $createData['user_id'] = $userId;

        if ($request->hasFile('attachment')) {
            $file = $request->attachment;
            $attachment = $file->store('attachment/attendance', 'public');
        }

        if ($request->has('type') && $request->type == 'rest' && $request->hasFile('attachment_rest')) {
            $file = $request->attachment_rest;
            $attachment_rest = $file->store('attachment/attendance/rest', 'public');
        }

        if ($request->has('type') && $request->type == 'overtime' && $request->hasFile('attachment_overtime')) {
            $file = $request->attachment_overtime;
            $attachment_overtime = $file->store('attachment/attendance/overtime', 'public');
        }

        if ($request->has('type') && $request->type == 'attendance') {
            if (isset($attendance) && $attendance->clock_in != null && $attendance->clock_out == null) {
                $createData['id'] = $attendance->id;
                $createData['clock_out'] = now();
                if ($request->hasFile('attachment')) {
                    $createData['attachment_clock_out'] = $attachment;
                }
                $createData['location_clock_out'] = $request->location;
            } else {
                $createData['id'] = null;
                $createData['clock_in'] = now();
                if ($request->hasFile('attachment')) {
                    $createData['attachment_clock_in'] = $attachment;
                }
                $createData['location_clock_in'] = $request->location;
            }
        }

        if ($request->has('type') && $request->type == 'rest') {
            if (isset($attendance) && $attendance['start_rest'] == null) {
                $createData['id'] = $attendance->id;
                $createData['start_rest'] = now();
                $createData['location_start_rest'] = $request->location_rest;
                $createData['attachment_start_rest'] = $attachment_rest;
            } else if (isset($attendance) && $attendance['start_rest'] != null && $attendance['end_rest'] == null) {
                $createData['id'] = $attendance->id;
                $createData['end_rest'] = now();
                $createData['location_end_rest'] = $request->location_rest;
                $createData['attachment_end_rest'] = $attachment_rest;
            }
        }

        if ($request->has('type') && $request->type == 'overtime') {
            if (isset($attendance) && $attendance['start_overtime'] == null && $attendance['end_overtime'] == null) {
                $createData['id'] = $attendance->id;
                $createData['start_overtime'] = now();
                $createData['location_start_overtime'] = $request->location_overtime;
                $createData['attachment_start_overtime'] = $attachment_overtime;
            } else if (isset($attendance) && $attendance['start_overtime'] != null && $attendance['end_overtime'] == null) {
                $createData['id'] = $attendance->id;
                $createData['end_overtime'] = now();
                $createData['location_end_overtime'] = $request->location_overtime;
                $createData['attachment_end_overtime'] = $attachment_overtime;
            }else{ 
                $createData['id'] = null;
                $createData['clock_in'] = null;
                $createData['start_overtime'] = now();
                $createData['location_start_overtime'] = $request->location_overtime;
                $createData['attachment_start_overtime'] = $attachment_overtime;
            }
        }

        return $createData;
    }

    public function getDataVisitRecord(Request $request)
    {
        $columns = [
            'id',
            'account_id',
            'address',
            'link_address',
            'photo',
            'visited',
            'contact',
            'is_approved',
            'note',
            'created_at',
        ];

        $userId = Auth::user()->id ?? session('id');
        $keyword = $request->search;
        $date = $request->date ?? now()->format('Y-m-d');
        $per_page = $request->per_page ?? 10;
        $all = $request->all;
        $group_by = $request->group_by;

        $limit = limitList($per_page);
        $data = MlAbsensiStaff::where('account_id', $userId)->orderBy('id', 'desc')
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            });

        $data = ($all == true) ? $data->get() : $data->paginate($limit);

        if ($group_by) {
            $data = $data->groupBy($group_by);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }

    public function storeDataVisitRecord(Request $request)
    {
        try {
            $data = $request->all();
            
            if ($request->hasFile('photo')) {
                $file = $request->photo;
                $data['photo'] = $file->store('appsensi/kunjungan', 'public');
            }
            
            return $this->atomic(function () use ($data) {
                $data['account_id'] = Auth::user()->id ?? session('id');
                $data['contact'] = [
                    'name' => $data['contact_name'],
                    'wa' => $data['contact_wa'],
                    'email' => $data['contact_email'],
                ];
    
                $create = MlAbsensiStaff::create($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Tambahkan',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Tambahkan',
            ]);
        }
    }

    public function showNoteVisitRecord($id)
    {
        try {
            $data = MlAbsensiStaff::find($id);

            $result = $data->note !== null ? [
                "id" => $data->id,
                "title" => $data->note['title'] ?? '',
                "category" => $data->note['category'] ?? '',
                "description" => $data->note['description'] ?? '',
            ] : null;

            return response()->json([
                'status' => true,
                'message' => 'Data Berhasil di Dapatkan',
                'data' => $result,
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Catatan Gagal di Dapatkan',
            ]);
        }
    }

    public function storeNoteVisitRecord(Request $request, $id)
    {
        try {
            $data = $request->all();
            return $this->atomic(function () use ($data, $id) {
                $data['note'] = [
                    "title" => $data['title'],
                    "category" => $data['category'],
                    "description" => $data['description'],
                ];

                $create = MlAbsensiStaff::find($id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Catatan Berhasil di Tambahkan',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Catatan Gagal di Tambahkan',
            ]);
        }
    }

    public function destroyVisitRecord($id)
    {
        try {
            return $this->atomic(function () use ($id) {
                $create = MlAbsensiStaff::find($id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Berhasil di Hapus',
                ]);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data Gagal di Hapus',
            ]);
        }
    }
}
