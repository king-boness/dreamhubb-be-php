<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * 🟢 Globálny upload obrázka
     * Accepts: form-data: file (File), folder (string, optional)
     * Returns: url, provider meta
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file'   => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
            'folder' => 'nullable|string',
        ]);

        $file   = $request->file('file');
        $folder = $request->input('folder', 'uploads');
        $driver = env('UPLOAD_DRIVER', config('filesystems.default', 'public'));

        if ($driver === 'cloudinary') {
            $upload = Cloudinary::upload($file->getRealPath(), [
                'folder' => $folder,
                // voliteľne: 'transformation' => [['quality' => 'auto', 'fetch_format' => 'auto']],
            ]);

            return response()->json([
                'status'     => 'success',
                'driver'     => $driver,
                'url'        => $upload->getSecurePath(),
                'public_id'  => $upload->getPublicId(),
                'format'     => $upload->getExtension(),
                'bytes'      => $upload->getSize(),
                'width'      => $upload->getWidth(),
                'height'     => $upload->getHeight(),
            ]);
        }

        // Lokálny fallback
        $path = $file->store("public/{$folder}");
        $url  = Storage::url($path);

        return response()->json([
            'status' => 'success',
            'driver' => $driver,
            'url'    => $url,
            'path'   => $path,
        ]);
    }
}
