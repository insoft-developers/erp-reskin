<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\MlAccount;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $data = Notification::orderBy('id', 'desc')->limit(30)->get();
        $view = 'notification';

        return view('main.notification.index', compact('view', 'data'));
    }

    public function show($id)
    {
        $data = Notification::find($id);
        $view = 'notification-show';

        return view('main.notification.show', compact('view', 'data'));
    }

    public function markAllAsRead()
    {
        session(['read_all_notification' => true]);

        return response()->json(['success' => session('read_all_notification')]);
    }

    public function showPopupNotif()
    {
        return view('main.popup');
    }

    public function hidePopup()
    {
        MlAccount::find(session('id'))->update(['popup_show' => 0]);

        return response()->json(['success' => true]);
    }
}
