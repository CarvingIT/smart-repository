<?php

namespace App\Http\Controllers;

use App\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $alerts = auth()->user()->alerts()
            ->orderBy('created_at', 'DESC')
            ->simplePaginate(10);

        return view('alerts.index', [
            'alerts' => $alerts,
        ]);
    }

    public function open($alertId)
    {
        $alert = Alert::where('user_id', auth()->id())
            ->where('id', $alertId)
            ->firstOrFail();

        if (!$alert->isRead()) {
            $alert->markAsRead();
        }

        if (!empty($alert->url)) {
            return redirect($alert->url);
        }

        return redirect()->route('alerts.index');
    }

    public function markRead($alertId)
    {
        $alert = Alert::where('user_id', auth()->id())
            ->where('id', $alertId)
            ->firstOrFail();

        $alert->markAsRead();
        return redirect()->back()->with('alert-success', __('Alert marked as read.'));
    }

    public function markUnread($alertId)
    {
        $alert = Alert::where('user_id', auth()->id())
            ->where('id', $alertId)
            ->firstOrFail();

        $alert->markAsUnread();
        return redirect()->back()->with('alert-success', __('Alert marked as unread.'));
    }
}
