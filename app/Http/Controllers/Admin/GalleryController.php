<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private const ALLOWED_MIME = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif',
        'image/svg+xml', 'video/mp4', 'video/webm',
        'application/pdf',
    ];

    private const MAX_MB = 20;

    public function index(Request $request)
    {
        $query = MediaFile::latest();

        if ($request->filled('folder') && $request->folder !== 'all') {
            $query->where('folder', $request->folder);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $files   = $query->paginate(30)->withQueryString();
        $folders = MediaFile::select('folder')->distinct()->pluck('folder')->filter()->sort()->values();

        return view('admin.gallery.index', compact('files', 'folders'));
    }

    public function store(Request $request)
    {
        $maxKb = self::MAX_MB * 1024;
        $request->validate([
            'files'    => ['required', 'array', 'min:1'],
            'files.*'  => ['file', "max:{$maxKb}", 'mimetypes:' . implode(',', self::ALLOWED_MIME)],
            'folder'   => ['nullable', 'string', 'max:80'],
        ]);

        $folder    = Str::slug($request->input('folder', 'general')) ?: 'general';
        $uploaded  = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $ext          = $file->getClientOriginalExtension();
            $storedName   = Str::uuid() . '.' . $ext;
            $path         = "gallery/{$folder}/{$storedName}";

            Storage::disk('public')->putFileAs("gallery/{$folder}", $file, $storedName);

            $media = MediaFile::create([
                'name'      => pathinfo($originalName, PATHINFO_FILENAME),
                'file_name' => $storedName,
                'mime_type' => $file->getMimeType(),
                'size'      => $file->getSize(),
                'disk'      => 'public',
                'path'      => $path,
                'folder'    => $folder,
            ]);

            $uploaded[] = $media;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'files'   => collect($uploaded)->map(fn ($m) => [
                    'id'   => $m->id,
                    'name' => $m->name,
                    'url'  => $m->url,
                ]),
            ]);
        }

        return back()->with('success', count($uploaded) . ' file(s) uploaded.');
    }

    public function update(Request $request, MediaFile $galleryFile)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alt'  => ['nullable', 'string', 'max:255'],
        ]);

        $galleryFile->update($request->only('name', 'alt'));

        return response()->json(['success' => true, 'file' => $galleryFile->fresh()]);
    }

    public function destroy(MediaFile $galleryFile)
    {
        Storage::disk($galleryFile->disk)->delete($galleryFile->path);
        $galleryFile->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'File deleted.');
    }
}
