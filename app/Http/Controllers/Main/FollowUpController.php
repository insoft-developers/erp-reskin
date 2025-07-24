<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\FollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    public function index()
    {
        $view = 'follow-up';
        $followup = FollowUp::orderBy('id', 'asc')->where('account_id', session('id'))->where('type', 'followup')->get();
        $upselling = FollowUp::orderBy('id', 'asc')->where('account_id', session('id'))->where('type', 'upselling')->get();

        return view('main.crm.followup.index', compact('view', 'followup', 'upselling'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = [$request->all()];
        // GET KEY ON THIS ARRAY
        $key = array_keys($data[0]);
        $type = $data[0]['type'];

        foreach ($key as $value) {
            $name = str_replace('_', ' ', $value);
            $name = ucfirst($name);
            if ($name != ' token' && $name != ' method' && $name != 'Type') {
                $text = $data[0][$value];

                $check = FollowUp::where('account_id', session('id'))
                                            ->where('name', $name)
                                            ->where('type', $type)
                                            ->first();
    
                if ($check) {
                    $dataUpdate = ([
                        'name' => $name,
                        'text' => $text,
                        'type' => $type,
                    ]);

                    $check->update($dataUpdate);
                }
            }
		}

        return redirect()->back()->with('success', 'Data Berhasil di Ubah');
    }
}
