<?php
namespace App\Traits;

use App\Models\Branch;
use App\Models\MlAccount;

trait UserTrait
{
    public function setUserId(int $userId) {
        $user = MlAccount::findorFail($userId);
        if($user->role_code == 'staff') {
            $branch = Branch::findorFail($user->branch_id);
            return $branch->account_id;
        } else {
            return $userId;
        }
    }   
}