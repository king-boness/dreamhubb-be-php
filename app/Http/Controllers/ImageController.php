<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageController extends Controller
{
    public static function uploadImage(Request $request, $registration = false)
{
    // 🧩 LOG pre debug
    \Log::info('🧩 UPLOAD IMAGE DEBUG START', [
        'hasFile' => $request->hasFile('profile_picture'),
        'file_exists' => $request->file('profile_picture') !== null,
        'file_keys' => array_keys($request->allFiles()),
        'input_keys' => array_keys($request->all()),
    ]);

    // ak neprišiel súbor, logni varovanie a vráť null namiesto pádu
    if (!$request->hasFile('profile_picture')) {
        \Log::warning('⚠️ No file detected in uploadImage()');
        return null;
    }

    // určenie cieľovej zložky
    $uploadFolder = $registration
        ? 'users/' . $request->email . '/profile_pictures'
        : 'users/' . Auth::user()->email . '/profile_pictures';

    $image = $request->file('profile_picture');

    // ešte malá poistka — ak by sa aj tak dostal null
    if (!$image) {
        \Log::error('❌ $request->file("profile_picture") is null, aborting upload');
        return null;
    }

    $imageName = $image->getClientOriginalName();

    // uloženie na Cloudinary
    $result = $image->storeOnCloudinaryAs($uploadFolder, $imageName, ['resource_type' => 'image']);

    // ak to nie je počas registrácie, aktualizuj profil usera
    if (!$registration && Auth::check()) {
        User::where('email', Auth::user()->email)->update([
            'profile_picture' => $result->getSecurePath(),
        ]);
    }

    \Log::info('✅ UPLOAD SUCCESS', [
        'path' => $result->getSecurePath(),
    ]);

    return $result->getSecurePath();
}

    public function deleteImage()
    {
        $user = Auth::user();
        $image = $user->profile_picture;
        $imageName = explode('/', $image);
        $imageName = substr($imageName[10], 0, -4);
        if ($image) {
            Cloudinary::destroy('users/' . $user->email . '/profile_pictures/' . $imageName);
            User::where('email', $user->email)->update([
                'profile_picture' => null,
            ]);
            // Storage::disk('public')->delete('users/' . $user->email . '/profile_pictures/' . basename($user->profile_picture));
            return response()->json([
                'message' => 'Image Deleted Successfully'
            ], 200);
        } else {
            return response()->json([
                'error' => 'Image not found'
            ], 404);
        }
    }

    public function updateImage(Request $request)
    {
        $this->deleteImage();
        $this->uploadImage($request);
    }
}
