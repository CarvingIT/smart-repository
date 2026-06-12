<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\SystemInfoService;
use App\Mail\SmtpTestMail;

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

    /**
     * Show the SMTP test form.
     */
    public function smtpTestForm()
    {
        return view('smtp-test', [
            'title' => 'Test SMTP Configuration',
            'activePage' => 'System Information',
            'titlePage' => 'Test SMTP Configuration',
        ]);
    }

    /**
     * Send a test SMTP email.
     */
    public function sendSmtpTest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        try {
            Mail::to($validated['email'])->send(new SmtpTestMail());

            return redirect()
                ->route('admin.smtp.test.form')
                ->with('alert-success', 'Test email sent successfully to ' . $validated['email'] . '.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->with('alert-danger', 'Unable to send the test email. Please verify the SMTP configuration and try again.');
        }
    }
}
