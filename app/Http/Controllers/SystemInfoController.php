<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemInfoService;

class SystemInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Show the system information page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $service = new SystemInfoService();
        $system_info = $service->getAllInfo();
        
        return view('system-info', [
            'title' => 'System Information',
            'activePage' => 'System Information',
            'titlePage' => 'System Information',
            'systemInfo' => $system_info,
        ]);
    }
}
