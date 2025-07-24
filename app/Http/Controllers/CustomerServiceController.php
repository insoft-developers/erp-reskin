<?php

namespace App\Http\Controllers;

use App\Models\MdCustomerService;
use App\Models\MdCustomerServiceMessageTemplate;
use App\Models\MdMessageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerServiceController extends Controller
{
    public function index()
    {
        $data['view'] = 'uknown';
        $user_id = $this->get_owner_id(session('id'));
        $user = DB::table('ml_accounts')->whereId($user_id)->first();
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;
        $data['name'] = $user->fullname;
        return view('main.customer_service.index', $data);
    }

    public function getData(Request $request)
    {
        $userId = $this->get_owner_id($request->session()->get('id'));
        $query = MdCustomerService::query();

        $query->where('user_id', $userId);
        return DataTables::of($query)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('phone', function ($row) {
                return $row->phone ?? 'Belum Scan Device';
            })
            ->addColumn('is_active', function ($row) {
                return $row->is_active === 1 ? '<span class="badge text-bg-success text-white">Active</span>' : ($row->is_active === 0 ? '<span class="badge text-bg-danger text-white">Inactive</span>' : '<span class="badge text-bg-warning">Waiting</span>');
            })
            ->addColumn('action', function ($row) {
                $html = '';
                if ($row->scan_url) {
                    if ($row->is_active === -1) {
                        $html .= '<div class="flex gap-[2px]">';
                        $html .= '<a href="' . $row->scan_url . '" target="_blank" class="btn btn-sm btn-primary">Detail Device</a>';
                        $html .= '<button type="button" class="btn btn-sm btn-warning scan-btn" data-id="' . $row->uuid . '">Scan</button>';
                        $html .= '</div>';
                    } else {
                        $html .= '<div class="flex gap-[2px]">';
                        $html .= '<a href="' . $row->scan_url . '" target="_blank" class="btn btn-sm btn-primary">Detail Device</a>';
                        $html .= '<a href="/customer-service/' . $row->id . '/templates" class="btn btn-sm btn-warning">Template</a>';
                        $html .= '<button type="button" class="btn btn-sm btn-danger logout-device-btn" data-id="' . $row->uuid . '">Logout</button>';
                        $html .= '</div>';
                    }
                } else {
                    //
                }

                return $html;
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function show(Request $request, $id)
    {
        $data['view'] = 'uknown';
        $data['id'] = $id;

        $current = MdCustomerServiceMessageTemplate::whereCs_id($id)->first();
        $templates = MdMessageTemplate::get();

        $data['form'] = [];
        foreach ($templates as $key => $temp) {
            $data['form'][] = [
                'title' => $temp->title,
                'key' => $temp->key,
                'value' => $current ? $current->{"template_" . $temp->key} : $temp->template,
                'info' => $temp->info,
            ];
        }

        return view('main.customer_service.show', $data);
    }

    public function showGetData(Request $request)
    {
        $userId = $this->get_owner_id($request->session()->get('id'));
        $query = MdCustomerServiceMessageTemplate::query();

        // $query->where('cs_id', $request->cs_id);
        return DataTables::of($query)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('template_key', function ($row) {
                return $row->msg_template->key;
            })
            ->addColumn('template_title', function ($row) {
                return $row->msg_template->title;
            })
            ->addColumn('action', function ($row) {
                return '-';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function saveTemplate(Request $request, $id)
    {
        $templates = MdMessageTemplate::get();
        $data = Arr::except($request->all(), '_token');
        $cek = MdCustomerServiceMessageTemplate::whereCs_id($id)->first();
        if (!$cek) {
            // buat baru
            MdCustomerServiceMessageTemplate::create($data);
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } else {
            foreach ($data as $key => $dt) {
                $cek->{$key} = $dt;
            }
            $cek->save();
            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        }
    }
}
