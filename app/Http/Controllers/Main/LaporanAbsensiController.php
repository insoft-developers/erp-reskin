<?php

namespace App\Http\Controllers\Main;

use App\Exports\LaporanAbsensiExport;
use App\Exports\LaporanAbsensiExportByDate;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BusinessGroup;
use App\Models\MlAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'laporan-absensi';
        $data = $this->getData($request);
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        return view('main.report.attendance.index', compact('view', 'userKey', 'data', 'daysInMonth', 'from'));
    }

    public function data(Request $request)
    {
        $data = $this->getData($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('detail_user', function ($data) {
                $elem = "Nama Perusahaan/Toko : $data->nama_toko <br>";
                $elem .= "Nama Staff/Karyawan : $data->nama_staff <br>";
                $elem .= "Jam Kerja : $data->jam_kerja <br>";
                $elem .= "Hari Libur : $data->hari_libur <br>";
                $elem .= "Mulai Bekerja : $data->mulai_kerja";

                return $elem;
            })
            ->addColumn('clock_in', function ($data) {
                if ($data->clock_in != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$data->clock_in.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_clock_in.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_clock_in.'\', \'Attachment Clock In\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('clock_out', function ($data) {
                if ($data->clock_out != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$data->clock_out.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_clock_out.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_clock_out.'\', \'Attachment Clock Out\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('start_rest', function ($data) {
                $time = ($data->start_rest == null) ? null : Carbon::parse($data->start_rest)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_start_rest.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_start_rest.'\', \'Attachment Start Rest\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('end_rest', function ($data) {
                $time = ($data->end_rest == null) ? null : Carbon::parse($data->end_rest)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_end_rest.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_end_rest.'\', \'Attachment End Rest\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('start_overtime', function ($data) {
                $time = ($data->start_overtime == null) ? null : Carbon::parse($data->start_overtime)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_start_overtime.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_start_overtime.'\', \'Attachment Start Overtime\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('end_overtime', function ($data) {
                $time = ($data->end_overtime == null) ? null : Carbon::parse($data->end_overtime)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a href="'.$data->location_end_overtime.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_end_overtime.'\', \'Attachment End Overtime\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getData(Request $request)
    {
        $keyword = $request->keyword;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        $userId = session('id');
        $account = MlAccount::where('id', $userId)->first();

        $data = [];
        for ($i=1; $i <= $daysInMonth; $i++) {
            $day = $year.'-'.$month.'-'.$i;
            $dayNow = Carbon::parse($day)->format('D');
            $date = Carbon::parse($day)->format('Y-m-d');
            $columns = ['id', 'user_id', 'clock_in', 'clock_out', 'created_at'];

            $MlAccount = MlAccount::select(['id', 'username', 'branch_id', 'holiday'])->where('branch_id', $account->branch_id)->get();
            foreach ($MlAccount as $key => $value) {
                $dataAttendance = Attendance::orderBy('id', 'desc')
                ->where('user_id', $value->id)
                ->select($columns)
                ->where(function ($query) use ($date) {
                    $query->whereDate('created_at', $date)->orWhereDate('clock_in', $date)->orWhereDate('clock_out', $date);
                })
                ->where(function ($result) use ($keyword, $columns) {
                    foreach ($columns as $column) {
                        if ($keyword != '') {
                            $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                        }
                    }
                })->first();

                $holiday = isset($value->holiday) && is_array(json_decode($value->holiday, true)) ? implode(', ', json_decode($value->holiday)) : '-';
                $dayHoliday = dayFormatEn($holiday);
                $checkHolidayUser = ($dayHoliday == $dayNow) ? true : false;
    
                $result['date'] = $i;
                $result['month'] = $month;
                $result['year'] = $year;
                $result['holiday'] = $checkHolidayUser;
                $result['attendance'] = (isset($dataAttendance->clock_in) && isset($dataAttendance->clock_out)) ? true : false;
                $result['user'] = $value->fullname ?? '';
    
                $data[$value->id][] = $result;
            }
        }

        return $data;
    }

    public function export(Request $request)
    {
        $data = $this->getData($request);
        $date = $request->month . ' ' . $request->year;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        
        // EXPORT WITH MAATWEBSITE
        return Excel::download(new LaporanAbsensiExport($data, $daysInMonth), 'Laporan Absensi ' . $date . '.xlsx');
    }

    // BYDATE
    public function indexByDate(Request $request)
    {
        $userKey = $request->user_key ?? null;
        $from = $request->from ?? 'desktop';
        $view = 'laporan-absensi';
        $data = $this->getDataByDate($request);
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        return view('main.report.attendance.indexByDate', compact('view', 'userKey', 'data', 'daysInMonth', 'from'));
    }

    public function dataByDate(Request $request)
    {
        $data = $this->getDataByDate($request);

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->addColumn('detail_user', function ($data) {
                $elem = "Nama Perusahaan/Toko : $data->nama_toko <br>";
                $elem .= "Nama Staff/Karyawan : $data->nama_staff <br>";
                $elem .= "Jam Kerja : $data->jam_kerja <br>";
                $elem .= "Hari Libur : $data->hari_libur <br>";
                $elem .= "Mulai Bekerja : $data->mulai_kerja";

                return $elem;
            })
            ->addColumn('clock_in', function ($data) {
                if ($data->clock_in != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$data->clock_in.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_clock_in.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_clock_in.'\', \'Attachment Clock In\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('clock_out', function ($data) {
                if ($data->clock_out != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$data->clock_out.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_clock_out.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_clock_out.'\', \'Attachment Clock Out\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('start_rest', function ($data) {
                $time = ($data->start_rest == null) ? null : Carbon::parse($data->start_rest)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_start_rest.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_start_rest.'\', \'Attachment Start Rest\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('end_rest', function ($data) {
                $time = ($data->end_rest == null) ? null : Carbon::parse($data->end_rest)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_end_rest.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_end_rest.'\', \'Attachment End Rest\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('start_overtime', function ($data) {
                $time = ($data->start_overtime == null) ? null : Carbon::parse($data->start_overtime)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a target="_blank" href="'.$data->location_start_overtime.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_start_overtime.'\', \'Attachment Start Overtime\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->addColumn('end_overtime', function ($data) {
                $time = ($data->end_overtime == null) ? null : Carbon::parse($data->end_overtime)->format('H:i');

                if ($time != null) {
                    $elem = '<div class="container">';
                    $elem .= '    <div class="row justify-content-center">';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <h1>'.$time.'</h1>';
                    $elem .= '        </div>';
                    $elem .= '        <div class="col-md-12 text-center">';
                    $elem .= '            <a href="'.$data->location_end_overtime.'" class="btn-primary p-2" style="border-radius: 50%;"><i class="fa fa-map-marker"></i></a>';
                    $elem .= '            <a href="javascript:void(0)" onclick="showAttachmentModal(\''.$data->attachment_end_overtime.'\', \'Attachment End Overtime\')" class="btn-success p-2" style="border-radius: 50%;"><i class="fa fa-file"></i></a>';
                    $elem .= '        </div>';
                    $elem .= '    </div>';
                    $elem .= '</div>';
                }

                return $elem ?? '';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function getDataByDate(Request $request)
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

        $keyword = $request->keyword;
        $date = $request->date ?? now()->format('Y-m-d');
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $per_page = $request->per_page ?? 10;

        $userId = session('id');
        $ownerId = $this->get_owner_id($userId);
        $account = MlAccount::where('id', $userId)->first();
        $branchId = MlAccount::where('branch_id', $account->branch_id)->pluck('id');

        $dataAttendance = Attendance::orderBy('id', 'desc')
            ->whereIn('user_id', $branchId)
            ->select($columns)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date)->orWhere('clock_in', $date)->orWhere('clock_out', $date);
            })
            // ->when($month, function ($query) use ($month) {
            //     $query->whereMonth('created_at', $month)->orWhereMonth('clock_in', $month)->orWhereMonth('clock_out', $month);
            // })
            // ->when($year, function ($query) use ($year) {
            //     $query->whereYear('created_at', $year)->orWhereYear('clock_in', $year)->orWhereYear('clock_out', $year);
            // })
            ->where(function ($result) use ($keyword, $columns) {
                foreach ($columns as $column) {
                    if ($keyword != '') {
                        $result->orWhere($column, 'LIKE', '%' . $keyword . '%');
                    }
                }
            })->get();

        $bussiness_group = BusinessGroup::where('user_id', $ownerId)->first();
        $not_attendance = MlAccount::whereNotIn('id', $dataAttendance->pluck('user_id'))->where('branch_id', $account->branch_id)->select(['id', 'fullname', 'clock_in', 'clock_out', 'holiday'])->get();

        foreach ($dataAttendance as $key => $value) {
            $value['nama_toko'] = $bussiness_group->branch_name ?? null;
            $value['nama_staff'] = $value->user->fullname ?? '';

            $clock_in_account = isset($value->user->clock_in) ? Carbon::parse($value->user->clock_in) : '';
            $clock_out_account = isset($value->user->clock_out) ? Carbon::parse($value->user->clock_out) : '';
            $holiday = isset($value->user->holiday) && ($value->user->holiday != "null") && ($value->user->holiday != '') ? implode(', ', json_decode($value->user->holiday)) : '-';
            // dd($value->user->holiday);

            $value['jam_kerja'] = isset($value->user->clock_in) && isset($value->user->clock_out) ? ($clock_in_account->format('H:i'). ' - ' . $clock_out_account->format('H:i')) : '';
            $value['hari_libur'] = $holiday;
            $value['mulai_kerja'] = isset($value->user->created_at) ? Carbon::parse($value->user->created_at)->format('Y-m-d') : '';
            $value['attachment_clock_in'] = asset('storage/'. $value->attachment_clock_in);
            $value['attachment_clock_out'] = asset('storage/'. $value->attachment_clock_out);
            $value['attachment_start_rest'] = asset('storage/'. $value->attachment_start_rest);
            $value['attachment_end_rest'] = asset('storage/'. $value->attachment_end_rest);
            $value['attachment_start_overtime'] = asset('storage/'. $value->attachment_start_overtime);
            $value['attachment_end_overtime'] = asset('storage/'. $value->attachment_end_overtime);

            $value['clock_in'] = Carbon::parse($value->clock_in)->format('H:i');
            $value['clock_out'] = ($value->clock_out != null) ? Carbon::parse($value->clock_out)->format('H:i') : '';

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

        // foreach ($not_attendance as $key => $value) {
        //     $value['nama_toko'] = $bussiness_group->branch_name;
        //     $value['nama_staff'] = $value->fullname;

        //     $clock_in_account = Carbon::parse($value->clock_in) ?? '';
        //     $clock_out_account = Carbon::parse($value->clock_out) ?? '';
        //     $holiday = implode(', ', json_decode($value->holiday)) ?? '-';

        //     $value['jam_kerja'] = $clock_in_account. ' - ' . $clock_out_account;
        //     $value['hari_libur'] = $holiday;
        //     $value['mulai_kerja'] = Carbon::parse($value->created_at)->format('Y-m-d');
        // }

        // $data['attendance'] = $dataAttendance;
        // $data['not_attendance'] = $not_attendance;

        return $dataAttendance;
    }

    public function exportByDate(Request $request)
    {
        $data = $this->getDataByDate($request);
        $date = $request->month . ' ' . $request->year;
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        
        // EXPORT WITH MAATWEBSITE
        return Excel::download(new LaporanAbsensiExportByDate($data, $daysInMonth), 'Laporan Absensi ' . $date . '.xlsx');
    }
}
