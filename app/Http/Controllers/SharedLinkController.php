<?php

namespace App\Http\Controllers;

use App\Document;
use App\SharedLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SharedLinkController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            $sharedLinks = SharedLink::with(['document', 'user'])->latest()->paginate(15);
        } else {
            $sharedLinks = SharedLink::with('document')->where('user_id', Auth::id())->latest()->paginate(15);
        }
        return view('shared-links.index', compact('sharedLinks'));
    }

    public function create(Document $document)
    {
        // Check if user has permission to share this document
        $this->authorize('create', [SharedLink::class, $document->id]);
        
        return view('shared-links.create', compact('document'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:documents,id',
            'password' => 'nullable|string|min:6',
            'expires_at' => 'nullable|date|after:now',
            'permission_level' => 'required|in:download',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user has permission to share this document
        $this->authorize('create', [SharedLink::class, $request->document_id]);

        $sharedLink = SharedLink::create([
            'document_id' => $request->document_id,
            'user_id' => Auth::id(),
            'token' => Str::random(40),
            'password' => $request->password ? bcrypt($request->password) : null,
            'expires_at' => $request->expires_at,
            'permission_level' => $request->permission_level,
            'description' => $request->description,
            'is_active' => true,
            'downloadable' => true,
        ]);

        return redirect()->route('shared-links.index')->with('success', 'Shared link created successfully.');
    }

    public function publicView(Request $request, $token)
    {
        $sharedLink = SharedLink::where('token', $token)->where('is_active', true)->firstOrFail();

        if ($sharedLink->expires_at && $sharedLink->expires_at->isPast()) {
            return abort(404);
        }

        // Check if this link requires a password
        if ($sharedLink->password) {
            // Check if password has been verified in this session
            $verifiedLinks = session('verified_links', []);
            
            if (!in_array($token, $verifiedLinks)) {
                return view('shared-links.password', compact('sharedLink'));
            }
        }

        return view('shared-links.public-view', compact('sharedLink'));
    }

    public function verifyPassword(Request $request, $token)
    {
        $sharedLink = SharedLink::where('token', $token)->where('is_active', true)->firstOrFail();

        if ($sharedLink->expires_at && $sharedLink->expires_at->isPast()) {
            return abort(404);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!$sharedLink->password || !password_verify($request->password, $sharedLink->password)) {
            return redirect()->route('shared-links.public-view', $token)
                ->withErrors(['password' => 'Invalid password. Please try again.']);
        }

        // Password is correct, store in session and redirect to public view
        $verifiedLinks = session('verified_links', []);
        $verifiedLinks[] = $token;
        session(['verified_links' => $verifiedLinks]);

        return redirect()->route('shared-links.public-view', $token);
    }

    public function download(Request $request, $token)
    {
        $sharedLink = SharedLink::where('token', $token)->where('is_active', true)->firstOrFail();

        if ($sharedLink->expires_at && $sharedLink->expires_at->isPast()) {
            return abort(404);
        }

        // Check if this link requires a password
        if ($sharedLink->password) {
            // Check if password has been verified in this session
            $verifiedLinks = session('verified_links', []);
            
            if (!in_array($token, $verifiedLinks)) {
                return redirect()->route('shared-links.public-view', $token)
                    ->withErrors(['password' => 'Please enter the password to access this document.']);
            }
        }

        if (!in_array($sharedLink->permission_level ?? 'download', ['download'])) {
            return abort(403, 'This file is not available for download.');
        }

        $document = $sharedLink->document;
        $storage_drive = empty($document->collection->storage_drive) ? 'local' : $document->collection->storage_drive;
        
        // Use Storage facade to get the file
        $filePath = \Storage::disk($storage_drive)->path($document->path);

        if (!file_exists($filePath)) {
            return abort(404, 'File not found.');
        }

        $filename = str_replace(['/', '\\'], '_', $document->title);

        return response()->download($filePath, $filename . '.' . $document->file_ext);
    }

    public function edit(SharedLink $sharedLink)
    {
        $this->authorize('update', $sharedLink);
        return view('shared-links.edit', compact('sharedLink'));
    }

    public function update(Request $request, SharedLink $sharedLink)
    {
        $this->authorize('update', $sharedLink);

        $request->validate([
            'password' => 'nullable|string|min:6',
            'expires_at' => 'nullable|date|after:now',
            'permission_level' => 'required|in:download',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = [
            'expires_at' => $request->expires_at,
            'permission_level' => $request->permission_level,
            'description' => $request->description,
            'is_active' => $request->input('is_active', 0),
            'downloadable' => true,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $sharedLink->update($data);

        return redirect()->route('shared-links.index')->with('success', 'Shared link updated successfully.');
    }

    public function destroy(SharedLink $sharedLink)
    {
        $this->authorize('delete', $sharedLink);
        $sharedLink->delete();
        return redirect()->route('shared-links.index')->with('success', 'Shared link deleted successfully.');
    }
}
