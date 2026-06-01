<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::latest();

        if ($request->filled('folder')) {
            $query->where('folder', $request->folder);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('mime')) {
            $query->where('mime_type', 'like', $request->mime . '%');
        }

        $files = $query->paginate($request->integer('per_page', 50));

        return response()->json([
            'success' => true,
            'data'    => $files->map(fn ($f) => [
                'id'         => $f->id,
                'name'       => $f->name,
                'url'        => $f->url,
                'mime_type'  => $f->mime_type,
                'size'       => $f->size,
                'human_size' => $f->human_size,
                'alt'        => $f->alt,
                'folder'     => $f->folder,
                'is_image'   => $f->is_image,
                'created_at' => $f->created_at,
            ]),
            'meta'    => [
                'current_page' => $files->currentPage(),
                'last_page'    => $files->lastPage(),
                'total'        => $files->total(),
                'per_page'     => $files->perPage(),
            ],
        ]);
    }
}
