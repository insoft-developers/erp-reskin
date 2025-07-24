<?php
namespace App\Traits;

use App\Models\Journal;
use App\Models\JournalList;
use Illuminate\Support\Facades\DB;
use App\Models\MlAccount;
use App\Models\Branch;

trait JournalTrait
{

	public function get_value_data($id, $asset_data_id, $account_code_id)
	{
        $query = JournalList::where('journal_id', $id)->where('asset_data_id', $asset_data_id)->where('account_code_id', $account_code_id);
        if ($account_code_id == 3 || $account_code_id == 4 || $account_code_id == 5 || $account_code_id == 6 || $account_code_id == 7 || $account_code_id == 11) {
            $nilai = $query->sum(DB::raw('credit-debet'));
            if ($nilai > 0) {
                $data['debet'] = 0;
                $data['credit'] = $nilai;
            } else {
                $data['debet'] = abs($nilai);
                $data['credit'] = 0;
            }
        } elseif ($account_code_id == 1 || $account_code_id == 2 || $account_code_id == 8 || $account_code_id == 9 || $account_code_id == 10 || $account_code_id == 12) {
            $nilai = $query->sum(DB::raw('debet-credit'));
            if ($nilai > 0) {
                $data['debet'] = $nilai;
                $data['credit'] = 0;
            } else {
                $data['debet'] = 0;
                $data['credit'] = abs($nilai);
            }
        }

        return $data;
	}

    public function user_id_staff($userid) {
        $user = MlAccount::findorFail($userid);
        if($user->role_code != 'staff') {
            return $user->id;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            return $branch->account_id;
        }
    }


    public function username_staff($userid) {
        $user = MlAccount::findorFail($userid);
        if($user->role_code != 'staff') {
            return $user->username;
        } else {
            $branch = Branch::findorFail($user->branch_id);
            $account = MlAccount::findorFail($branch->account_id);
            return $account->username;
        }
    }

}