<?php
namespace App\Traits;

use App\Models\UserActivityLogs;
use Illuminate\Support\Facades\DB;


trait LogUserTrait
{
    public function insert_user_log($userid, $page) {
        $log = new UserActivityLogs();
        $log->user_id = $userid;
        $log->page = $page;
        $log->created_at = date('Y-m-d H:i:s');
        $log->updated_at = date('Y-m-d H:i:s');
        $log->is_mobile = 1;
        $log->save(); 
    }
	
}