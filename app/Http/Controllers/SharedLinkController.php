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
        
        // Check if cloud storage
        $cloud_storages = ['google'];
        $driver = config("filesystems.disks.{$storage_drive}.driver");
        
        if (in_array($driver, $cloud_storages)) {
            return $this->downloadCloudFile($document, $storage_drive);
        }
        
        // For local storage
        try {
            $file_path = $document->path;
            
            // Extract filename from path and remove prefix
            $path_parts = explode('/', $file_path);
            $file_name = array_pop($path_parts);
            $file_name = preg_replace('/\d*_\d*_/', '', $file_name);
            $file_name = preg_replace('/,/', '', $file_name);
            
            // Get the full file path
            $fullPath = \Storage::disk($storage_drive)->path($file_path);
            
            if (!file_exists($fullPath)) {
                return abort(404, 'File not found.');
            }
            
            $mime = \Storage::disk($storage_drive)->mimeType($file_path);
            $size = \Storage::disk($storage_drive)->size($file_path);
            
            $response = [
                'Content-Type' => $mime,
                'Content-Length' => $size,
                'Content-Description' => 'File Transfer',
                'Content-Transfer-Encoding' => 'binary',
            ];
            
            // Force download for non-PDF files
            if ($mime != 'application/pdf') {
                $response['Content-Disposition'] = "attachment; filename={$file_name}";
            }
            
            ob_end_clean();
            
            return \Response::make(\Storage::disk($storage_drive)->get($file_path), 200, $response);
            
        } catch (\Exception $e) {
            return abort(500, 'Error downloading file: ' . $e->getMessage());
        }
    }
    
    private function downloadCloudFile($document, $storage_drive)
    {
        $filename = $document->path;
        $dir = '/';
        $recursive = false;
        $contents = collect(\Storage::disk($storage_drive)->listContents($dir, $recursive));
        
        $file = $contents
            ->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($filename, PATHINFO_FILENAME))
            ->where('extension', '=', pathinfo($filename, PATHINFO_EXTENSION))
            ->first();
        
        if (!$file) {
            return abort(404, 'File not found in cloud storage.');
        }
        
        $path_parts = explode('/', $filename);
        $file_name = array_pop($path_parts);
        $file_name = preg_replace('/\d*_\d*_/', '', $file_name);
        $file_name = preg_replace('/,/', '', $file_name);
        
        $mime = \Storage::disk($storage_drive)->mimeType($filename);
        $size = \Storage::disk($storage_drive)->size($filename);
        
        $response = [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Content-Description' => 'File Transfer',
            'Content-Transfer-Encoding' => 'binary',
        ];
        
        if ($mime != 'application/pdf') {
            $response['Content-Disposition'] = "attachment; filename={$file_name}";
        }
        
        ob_end_clean();
        
        return \Response::make(\Storage::disk($storage_drive)->get($filename), 200, $response);
    }

    public function viewer(Request $request, $token)
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
                return abort(403, 'Password required to view this document.');
            }
        }

        $document = $sharedLink->document;
        $storage_drive = empty($document->collection->storage_drive) ? 'local' : $document->collection->storage_drive;
        
        // Check if cloud storage
        $cloud_storages = ['google'];
        $driver = config("filesystems.disks.{$storage_drive}.driver");
        
        if (in_array($driver, $cloud_storages)) {
            // For cloud storage, redirect to download or show error
            return abort(501, 'Cloud storage viewer not implemented.');
        }
        
        // For local storage - serve the PDF directly
        try {
            $file_path = $document->path;
            
            // Get the full file path
            $fullPath = \Storage::disk($storage_drive)->path($file_path);
            
            if (!file_exists($fullPath)) {
                return abort(404, 'File not found.');
            }
            
            $mime = \Storage::disk($storage_drive)->mimeType($file_path);
            
            // Only serve PDFs in viewer
            if ($mime != 'application/pdf') {
                return abort(400, 'Only PDF files can be viewed.');
            }
            
            $response = [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline',
            ];
            
            return \Response::make(\Storage::disk($storage_drive)->get($file_path), 200, $response);
            
        } catch (\Exception $e) {
            return abort(500, 'Error viewing file: ' . $e->getMessage());
        }
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
